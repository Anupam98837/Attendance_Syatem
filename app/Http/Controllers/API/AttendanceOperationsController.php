<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithAttendance;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceOperationsController extends Controller
{
    use InteractsWithAttendance;

    public function dashboard(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $totalEmployees = $this->activeEmployeesBaseQuery()->count();
        $markedSummary = DB::table('employee_attendance')
            ->whereDate('attendance_date', $date)
            ->whereNull('deleted_at')
            ->selectRaw("
                COUNT(*) as total_marked,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN status = 'half_day' THEN 1 ELSE 0 END) as half_day_count,
                SUM(CASE WHEN status = 'pending_approval' THEN 1 ELSE 0 END) as pending_approval_count,
                SUM(CASE WHEN approval_status = 'rejected' OR status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
                SUM(CASE WHEN attendance_mode = 'offline' THEN 1 ELSE 0 END) as offline_count
            ")
            ->first();

        $leaveCount = DB::table('leave_requests')
            ->where('status', 'approved')
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->whereNull('deleted_at')
            ->count();

        $pendingSyncCount = DB::table('attendance_sync_queue')
            ->whereDate('queued_at', $date)
            ->whereNull('deleted_at')
            ->whereIn('sync_status', ['processing', 'pending_approval', 'sync_failed'])
            ->count();

        $locationExceptionCount = DB::table('attendance_logs')
            ->whereDate('punch_time', $date)
            ->whereNull('deleted_at')
            ->where('is_exception', 1)
            ->count();

        $activeSessionCount = DB::table('employee_attendance')
            ->whereDate('attendance_date', $date)
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->whereNull('deleted_at')
            ->count();

        $lateMinutesAvg = DB::table('employee_attendance')
            ->whereDate('attendance_date', $date)
            ->whereNull('deleted_at')
            ->whereNotNull('late_minutes')
            ->avg('late_minutes');

        $totalMarked = (int) ($markedSummary->total_marked ?? 0);

        return response()->json([
            'status' => 'success',
            'data' => [
                'date' => $date,
                'summary' => [
                    'total_employees' => $totalEmployees,
                    'marked' => $totalMarked,
                    'not_marked' => max(0, $totalEmployees - $totalMarked - $leaveCount),
                    'present' => (int) ($markedSummary->present_count ?? 0),
                    'late' => (int) ($markedSummary->late_count ?? 0),
                    'half_day' => (int) ($markedSummary->half_day_count ?? 0),
                    'on_leave' => $leaveCount,
                    'pending_approval' => (int) ($markedSummary->pending_approval_count ?? 0),
                    'rejected' => (int) ($markedSummary->rejected_count ?? 0),
                    'offline_records' => (int) ($markedSummary->offline_count ?? 0),
                    'pending_sync' => $pendingSyncCount,
                    'location_exceptions' => $locationExceptionCount,
                    'active_sessions' => $activeSessionCount,
                    'average_late_minutes' => $lateMinutesAvg !== null ? round((float) $lateMinutesAvg, 1) : 0,
                ],
            ],
        ]);
    }

    public function liveAttendance(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $perPage = max(1, min(200, (int) $request->query('per_page', 20)));

        $latestLogIds = DB::table('attendance_logs')
            ->whereDate('punch_time', $date)
            ->whereNull('deleted_at')
            ->selectRaw('employee_attendance_id, MAX(id) as id')
            ->groupBy('employee_attendance_id');

        $latestTrackIds = DB::table('attendance_location_tracks')
            ->whereNull('deleted_at')
            ->selectRaw('employee_attendance_id, MAX(id) as id')
            ->groupBy('employee_attendance_id');

        $query = $this->activeEmployeesBaseQuery()
            ->leftJoin('employee_attendance as ea', function ($join) use ($date) {
                $join->on('ea.employee_profile_id', '=', 'ep.id')
                    ->whereDate('ea.attendance_date', '=', $date)
                    ->whereNull('ea.deleted_at');
            })
            ->leftJoinSub($latestLogIds, 'latest_logs', function ($join) {
                $join->on('latest_logs.employee_attendance_id', '=', 'ea.id');
            })
            ->leftJoin('attendance_logs as al', 'al.id', '=', 'latest_logs.id')
            ->leftJoinSub($latestTrackIds, 'latest_tracks', function ($join) {
                $join->on('latest_tracks.employee_attendance_id', '=', 'ea.id');
            })
            ->leftJoin('attendance_location_tracks as alt', 'alt.id', '=', 'latest_tracks.id')
            ->leftJoin('departments as d', 'd.id', '=', 'ep.department_id')
            ->leftJoin('designations as dg', 'dg.id', '=', 'ep.designation_id')
            ->leftJoin('branches as b', 'b.id', '=', 'ep.branch_id')
            ->leftJoin('shifts as s', 's.id', '=', 'ep.shift_id')
            ->select([
                'ep.id as employee_profile_id',
                'ep.employee_code',
                'u.id as user_id',
                'u.uuid as user_uuid',
                'u.name',
                'u.email',
                'u.phone_number',
                'd.name as department_name',
                'dg.name as designation_name',
                'b.name as branch_name',
                's.name as shift_name',
                's.start_time as shift_start_time',
                's.end_time as shift_end_time',
                'ea.id as attendance_id',
                'ea.attendance_date',
                'ea.status as attendance_status',
                'ea.attendance_mode',
                'ea.work_mode',
                'ea.sync_status',
                'ea.approval_status',
                'ea.check_in_time',
                'ea.check_out_time',
                'ea.total_working_minutes',
                'ea.late_minutes',
                'ea.overtime_minutes',
                'ea.within_geofence',
                'ea.within_wifi_ip',
                'al.id as latest_log_id',
                'al.punch_type as latest_punch_type',
                'al.punch_time as latest_punch_time',
                'al.location_text as latest_location_text',
                'al.latitude as latest_latitude',
                'al.longitude as latest_longitude',
                'al.network_type as latest_network_type',
                'al.internet_status as latest_internet_status',
                'al.sync_status as latest_log_sync_status',
                'al.exception_reason as latest_exception_reason',
                'alt.recorded_at as latest_track_time',
                'alt.latitude as latest_track_latitude',
                'alt.longitude as latest_track_longitude',
                'alt.gps_accuracy_meters as latest_track_accuracy',
                'alt.network_type as latest_track_network_type',
                'alt.sync_status as latest_track_sync_status',
            ]);

        $this->applyEmployeeFilters($query, $request);

        if ($request->filled('status')) {
            $status = (string) $request->query('status');
            if ($status === 'pending' || $status === 'not_marked') {
                $query->whereNull('ea.id');
            } else {
                $query->where('ea.status', $status);
            }
        }

        if ($request->filled('q')) {
            $term = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($term) {
                $builder->where('u.name', 'like', $term)
                    ->orWhere('u.email', 'like', $term)
                    ->orWhere('u.phone_number', 'like', $term)
                    ->orWhere('ep.employee_code', 'like', $term);
            });
        }

        $paginator = $query->orderByRaw('CASE WHEN ea.check_in_time IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('u.name')
            ->paginate($perPage);

        $rows = collect($paginator->items())->map(function ($row) {
            $row->live_status = $row->attendance_id
                ? ($row->attendance_status ?: 'present')
                : 'not_marked';
            $row->active_session = !empty($row->check_in_time) && empty($row->check_out_time);
            return $row;
        })->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $rows,
            'pagination' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function attendanceIndex(Request $request)
    {
        [$fromDate, $toDate] = $this->resolveDateRange($request);
        $perPage = max(1, min(200, (int) $request->query('per_page', 20)));

        $query = $this->attendanceBaseQuery()
            ->when($fromDate, fn ($q) => $q->whereDate('ea.attendance_date', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->whereDate('ea.attendance_date', '<=', $toDate));

        $this->applyEmployeeFilters($query, $request);
        $this->applyAttendanceFilters($query, $request);

        if ($request->filled('q')) {
            $term = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($term) {
                $builder->where('u.name', 'like', $term)
                    ->orWhere('u.email', 'like', $term)
                    ->orWhere('u.phone_number', 'like', $term)
                    ->orWhere('ep.employee_code', 'like', $term);
            });
        }

        $paginator = $query->orderByDesc('ea.attendance_date')->orderByDesc('ea.id')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $paginator->items(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function pendingApprovals(Request $request)
    {
        $perPage = max(1, min(200, (int) $request->query('per_page', 20)));

        $query = DB::table('attendance_approvals as aa')
            ->join('users as u', 'u.id', '=', 'aa.user_id')
            ->leftJoin('employee_profiles as ep', 'ep.user_id', '=', 'u.id')
            ->leftJoin('employee_attendance as ea', 'ea.id', '=', 'aa.employee_attendance_id')
            ->leftJoin('attendance_logs as al', 'al.id', '=', 'aa.attendance_log_id')
            ->whereNull('aa.deleted_at')
            ->when($request->filled('status'), fn ($q) => $q->where('aa.status', $request->query('status')), fn ($q) => $q->where('aa.status', 'pending_approval'))
            ->select([
                'aa.*',
                'u.name',
                'u.email',
                'u.phone_number',
                'ep.employee_code',
                'ea.attendance_date',
                'ea.status as attendance_status',
                'ea.attendance_mode',
                'ea.work_mode',
                'ea.approval_status',
                'al.punch_type',
                'al.punch_time',
                'al.location_text',
                'al.latitude',
                'al.longitude',
                'al.network_type',
                'al.internet_status',
                'al.request_ip',
                'al.exception_reason',
            ]);

        $this->applyEmployeeFilters($query, $request);

        if ($request->filled('approval_type')) {
            $query->where('aa.approval_type', $request->query('approval_type'));
        }

        $paginator = $query->orderByDesc('aa.requested_at')->orderByDesc('aa.id')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $paginator->items(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function decideApproval(Request $request, int $id)
    {
        $validated = $request->validate([
            'decision' => 'required|string|in:approve,reject',
            'remarks' => 'sometimes|nullable|string',
        ]);

        $approval = DB::table('attendance_approvals')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$approval) {
            return response()->json(['status' => 'error', 'message' => 'Approval record not found'], 404);
        }

        $actor = $this->attendanceActor($request);
        $decisionStatus = $validated['decision'] === 'approve' ? 'approved' : 'rejected';

        DB::beginTransaction();

        try {
            DB::table('attendance_approvals')->where('id', $approval->id)->update([
                'status' => $decisionStatus,
                'approver_id' => $actor['id'] ?: null,
                'decided_at' => now(),
                'approver_remarks' => $validated['remarks'] ?? null,
                'updated_at' => now(),
            ]);

            $attendanceRecord = null;
            if (!empty($approval->employee_attendance_id)) {
                $attendanceRecord = DB::table('employee_attendance')->where('id', $approval->employee_attendance_id)->first();

                if ($attendanceRecord) {
                    DB::table('employee_attendance')->where('id', $attendanceRecord->id)->update([
                        'approval_status' => $decisionStatus,
                        'approved_by' => $actor['id'] ?: null,
                        'approved_at' => now(),
                        'remarks' => $validated['remarks'] ?? $attendanceRecord->remarks,
                        'updated_at' => now(),
                    ]);

                    $employee = $this->employeeContextByUserId((int) $attendanceRecord->user_id);
                    if ($employee) {
                        $attendanceRecord = $this->recalculateAttendanceRecord((int) $attendanceRecord->id, $employee, $decisionStatus);
                    }
                }
            }

            DB::commit();

            $this->createAttendanceAudit(
                $request,
                'attendance_approvals',
                $decisionStatus,
                'attendance_approvals',
                $approval->id,
                $validated['remarks'] ?? 'Attendance approval decision recorded.',
                (array) $approval,
                [
                    'decision' => $decisionStatus,
                    'attendance' => $attendanceRecord,
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => $decisionStatus === 'approved' ? 'Attendance approved successfully.' : 'Attendance rejected successfully.',
                'data' => [
                    'approval' => DB::table('attendance_approvals')->where('id', $approval->id)->first(),
                    'attendance' => $attendanceRecord,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Could not update approval.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function manualCorrection(Request $request, int $attendanceId)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'remarks' => 'sometimes|nullable|string',
            'check_in_time' => 'sometimes|nullable|date',
            'check_out_time' => 'sometimes|nullable|date|after_or_equal:check_in_time',
            'check_in_latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'check_in_longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'check_out_latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'check_out_longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'attendance_mode' => 'sometimes|nullable|string|in:online,offline,manual',
            'work_mode' => 'sometimes|nullable|string|in:office,field,wfh,hybrid',
            'status' => 'sometimes|nullable|string|max:40',
            'approval_status' => 'sometimes|nullable|in:approved,pending_approval,rejected',
        ]);

        $attendance = DB::table('employee_attendance')->where('id', $attendanceId)->whereNull('deleted_at')->first();
        if (!$attendance) {
            return response()->json(['status' => 'error', 'message' => 'Attendance record not found'], 404);
        }

        $employee = $this->employeeContextByUserId((int) $attendance->user_id);
        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee profile not found'], 404);
        }

        $actor = $this->attendanceActor($request);
        $oldAttendance = (array) $attendance;

        DB::beginTransaction();

        try {
            $updates = ['updated_at' => now()];

            foreach ([
                'check_in_time',
                'check_out_time',
                'check_in_latitude',
                'check_in_longitude',
                'check_out_latitude',
                'check_out_longitude',
                'attendance_mode',
                'work_mode',
            ] as $field) {
                if (array_key_exists($field, $validated)) {
                    $updates[$field] = $validated[$field];
                }
            }

            $updates['attendance_mode'] = $validated['attendance_mode'] ?? 'manual';
            $updates['remarks'] = $validated['remarks'] ?? $validated['reason'];
            $updates['approval_status'] = $validated['approval_status'] ?? 'approved';
            $updates['approved_by'] = $actor['id'] ?: null;
            $updates['approved_at'] = now();

            DB::table('employee_attendance')->where('id', $attendanceId)->update($updates);
            $finalRecord = $this->recalculateAttendanceRecord($attendanceId, $employee, $updates['approval_status']);

            if (!empty($validated['status'])) {
                DB::table('employee_attendance')->where('id', $attendanceId)->update([
                    'status' => $validated['status'],
                    'updated_at' => now(),
                ]);
                $finalRecord = DB::table('employee_attendance')->where('id', $attendanceId)->first();
            }

            $approvalId = $this->createAttendanceApproval([
                'user_id' => $attendance->user_id,
                'employee_attendance_id' => $attendanceId,
                'requested_by' => $actor['id'] ?: null,
                'approver_id' => $actor['id'] ?: null,
                'approval_type' => 'manual_correction',
                'status' => 'approved',
                'requested_at' => now(),
                'decided_at' => now(),
                'reason' => $validated['reason'],
                'approver_remarks' => $validated['remarks'] ?? null,
                'old_values' => $oldAttendance,
                'new_values' => (array) $finalRecord,
            ]);

            DB::commit();

            $this->createAttendanceAudit(
                $request,
                'employee_attendance',
                'manual_correction',
                'employee_attendance',
                $attendanceId,
                $validated['reason'],
                $oldAttendance,
                (array) $finalRecord,
                ['approval_id' => $approvalId]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Manual attendance correction saved successfully.',
                'data' => [
                    'attendance' => $finalRecord,
                    'approval_id' => $approvalId,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Could not save manual correction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reports(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:daily,monthly,late,absent,overtime,offline,location_exception,payroll',
            'date' => 'sometimes|nullable|date',
            'from' => 'sometimes|nullable|date',
            'to' => 'sometimes|nullable|date',
            'month' => 'sometimes|nullable|date_format:Y-m',
            'branch_id' => 'sometimes|nullable|integer|exists:branches,id',
            'department_id' => 'sometimes|nullable|integer|exists:departments,id',
        ]);

        [$fromDate, $toDate] = $this->resolveDateRange($request);
        $type = $validated['type'];

        if ($type === 'absent') {
            $date = $validated['date'] ?? $fromDate ?? now()->toDateString();
            $rows = $this->activeEmployeesBaseQuery()
                ->leftJoin('employee_attendance as ea', function ($join) use ($date) {
                    $join->on('ea.employee_profile_id', '=', 'ep.id')
                        ->whereDate('ea.attendance_date', '=', $date)
                        ->whereNull('ea.deleted_at');
                })
                ->leftJoin('departments as d', 'd.id', '=', 'ep.department_id')
                ->leftJoin('designations as dg', 'dg.id', '=', 'ep.designation_id')
                ->leftJoin('branches as b', 'b.id', '=', 'ep.branch_id')
                ->whereNull('ea.id')
                ->select([
                    'ep.id as employee_profile_id',
                    'ep.employee_code',
                    'u.id as user_id',
                    'u.name',
                    'u.email',
                    'u.phone_number',
                    'd.name as department_name',
                    'dg.name as designation_name',
                    'b.name as branch_name',
                ])
                ->orderBy('u.name')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $rows,
                'summary' => [
                    'type' => $type,
                    'date' => $date,
                    'count' => $rows->count(),
                ],
            ]);
        }

        if ($type === 'location_exception') {
            $query = DB::table('attendance_logs as al')
                ->join('users as u', 'u.id', '=', 'al.user_id')
                ->leftJoin('employee_profiles as ep', 'ep.id', '=', 'al.employee_profile_id')
                ->leftJoin('branches as b', 'b.id', '=', 'al.branch_id')
                ->whereNull('al.deleted_at')
                ->where('al.is_exception', 1)
                ->select([
                    'al.*',
                    'u.name',
                    'u.email',
                    'u.phone_number',
                    'ep.employee_code',
                    'b.name as branch_name',
                ])
                ->when($fromDate, fn ($q) => $q->whereDate('al.punch_time', '>=', $fromDate))
                ->when($toDate, fn ($q) => $q->whereDate('al.punch_time', '<=', $toDate));

            if (!empty($validated['branch_id'])) {
                $query->where('al.branch_id', $validated['branch_id']);
            }

            $rows = $query->orderByDesc('al.punch_time')->get();

            return response()->json([
                'status' => 'success',
                'data' => $rows,
                'summary' => [
                    'type' => $type,
                    'from' => $fromDate,
                    'to' => $toDate,
                    'count' => $rows->count(),
                ],
            ]);
        }

        if (in_array($type, ['monthly', 'payroll'], true)) {
            $query = DB::table('employee_attendance as ea')
                ->join('users as u', 'u.id', '=', 'ea.user_id')
                ->leftJoin('employee_profiles as ep', 'ep.id', '=', 'ea.employee_profile_id')
                ->leftJoin('departments as d', 'd.id', '=', 'ep.department_id')
                ->leftJoin('designations as dg', 'dg.id', '=', 'ep.designation_id')
                ->leftJoin('branches as b', 'b.id', '=', 'ep.branch_id')
                ->whereNull('ea.deleted_at')
                ->when($fromDate, fn ($q) => $q->whereDate('ea.attendance_date', '>=', $fromDate))
                ->when($toDate, fn ($q) => $q->whereDate('ea.attendance_date', '<=', $toDate));

            $this->applyEmployeeFilters($query, $request);

            $rows = $query->groupBy([
                'u.id',
                'u.name',
                'u.email',
                'u.phone_number',
                'ep.employee_code',
                'd.name',
                'dg.name',
                'b.name',
            ])->selectRaw("
                u.id as user_id,
                u.name,
                u.email,
                u.phone_number,
                ep.employee_code,
                d.name as department_name,
                dg.name as designation_name,
                b.name as branch_name,
                COUNT(ea.id) as marked_days,
                SUM(CASE WHEN ea.status = 'present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN ea.status = 'late' THEN 1 ELSE 0 END) as late_days,
                SUM(CASE WHEN ea.status = 'half_day' THEN 1 ELSE 0 END) as half_days,
                SUM(CASE WHEN ea.attendance_mode = 'offline' THEN 1 ELSE 0 END) as offline_days,
                SUM(COALESCE(ea.total_working_minutes, 0)) as total_working_minutes,
                SUM(COALESCE(ea.overtime_minutes, 0)) as total_overtime_minutes
            ")
                ->orderBy('u.name')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $rows,
                'summary' => [
                    'type' => $type,
                    'from' => $fromDate,
                    'to' => $toDate,
                    'count' => $rows->count(),
                ],
            ]);
        }

        $query = $this->attendanceBaseQuery()
            ->when($fromDate, fn ($q) => $q->whereDate('ea.attendance_date', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->whereDate('ea.attendance_date', '<=', $toDate));

        $this->applyEmployeeFilters($query, $request);

        if ($type === 'late') {
            $query->where('ea.late_minutes', '>', 0);
        } elseif ($type === 'overtime') {
            $query->where('ea.overtime_minutes', '>', 0);
        } elseif ($type === 'offline') {
            $query->where('ea.attendance_mode', 'offline');
        }

        $rows = $query->orderByDesc('ea.attendance_date')->orderBy('u.name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $rows,
            'summary' => [
                'type' => $type,
                'from' => $fromDate,
                'to' => $toDate,
                'count' => $rows->count(),
            ],
        ]);
    }

    public function offlineSyncLogs(Request $request)
    {
        $perPage = max(1, min(200, (int) $request->query('per_page', 20)));

        $query = DB::table('attendance_sync_queue as q')
            ->join('users as u', 'u.id', '=', 'q.user_id')
            ->leftJoin('employee_profiles as ep', 'ep.user_id', '=', 'u.id')
            ->whereNull('q.deleted_at')
            ->select([
                'q.*',
                'u.name',
                'u.email',
                'u.phone_number',
                'ep.employee_code',
            ]);

        if ($request->filled('sync_status')) {
            $query->where('q.sync_status', $request->query('sync_status'));
        }

        if ($request->filled('queue_type')) {
            $query->where('q.queue_type', $request->query('queue_type'));
        }

        if ($request->filled('device_id')) {
            $query->where('q.device_id', $request->query('device_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('q.queued_at', '>=', $request->query('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('q.queued_at', '<=', $request->query('to'));
        }

        $paginator = $query->orderByDesc('q.id')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $paginator->items(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function locationExceptions(Request $request)
    {
        $perPage = max(1, min(200, (int) $request->query('per_page', 20)));

        $query = DB::table('attendance_logs as al')
            ->join('users as u', 'u.id', '=', 'al.user_id')
            ->leftJoin('employee_profiles as ep', 'ep.id', '=', 'al.employee_profile_id')
            ->leftJoin('branches as b', 'b.id', '=', 'al.branch_id')
            ->whereNull('al.deleted_at')
            ->where('al.is_exception', 1)
            ->where(function ($builder) {
                $builder->where('al.exception_reason', 'like', '%geofence%')
                    ->orWhere('al.exception_reason', 'like', '%wifi%')
                    ->orWhere('al.exception_reason', 'like', '%gps%')
                    ->orWhere('al.exception_reason', 'like', '%device%');
            })
            ->select([
                'al.*',
                'u.name',
                'u.email',
                'u.phone_number',
                'ep.employee_code',
                'b.name as branch_name',
            ]);

        if ($request->filled('from')) {
            $query->whereDate('al.punch_time', '>=', $request->query('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('al.punch_time', '<=', $request->query('to'));
        }

        if ($request->filled('branch_id')) {
            $query->where('al.branch_id', $request->query('branch_id'));
        }

        $paginator = $query->orderByDesc('al.punch_time')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $paginator->items(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function leaveIndex(Request $request)
    {
        $perPage = max(1, min(200, (int) $request->query('per_page', 20)));

        $query = DB::table('leave_requests as lr')
            ->join('users as u', 'u.id', '=', 'lr.user_id')
            ->leftJoin('employee_profiles as ep', 'ep.id', '=', 'lr.employee_profile_id')
            ->leftJoin('leave_types as lt', 'lt.id', '=', 'lr.leave_type_id')
            ->leftJoin('departments as d', 'd.id', '=', 'ep.department_id')
            ->leftJoin('branches as b', 'b.id', '=', 'ep.branch_id')
            ->whereNull('lr.deleted_at')
            ->select([
                'lr.*',
                'u.name',
                'u.email',
                'u.phone_number',
                'ep.employee_code',
                'lt.name as leave_type_name',
                'd.name as department_name',
                'b.name as branch_name',
            ]);

        if ($request->filled('status')) {
            $query->where('lr.status', $request->query('status'));
        }

        if ($request->filled('from')) {
            $query->whereDate('lr.from_date', '>=', $request->query('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('lr.to_date', '<=', $request->query('to'));
        }

        $this->applyEmployeeFilters($query, $request);

        $paginator = $query->orderByDesc('lr.id')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $paginator->items(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function decideLeave(Request $request, int $leaveId)
    {
        $validated = $request->validate([
            'decision' => 'required|string|in:approve,reject',
            'remarks' => 'sometimes|nullable|string',
        ]);

        $leave = DB::table('leave_requests')->where('id', $leaveId)->whereNull('deleted_at')->first();
        if (!$leave) {
            return response()->json(['status' => 'error', 'message' => 'Leave request not found'], 404);
        }

        $actor = $this->attendanceActor($request);
        $status = $validated['decision'] === 'approve' ? 'approved' : 'rejected';

        DB::table('leave_requests')->where('id', $leaveId)->update([
            'status' => $status,
            'approved_by' => $actor['id'] ?: null,
            'approved_at' => now(),
            'rejection_reason' => $status === 'rejected' ? ($validated['remarks'] ?? null) : null,
            'updated_at' => now(),
        ]);

        $fresh = DB::table('leave_requests')->where('id', $leaveId)->first();

        $this->createAttendanceAudit(
            $request,
            'leave_requests',
            $status,
            'leave_requests',
            $leaveId,
            $validated['remarks'] ?? 'Leave request decision recorded.',
            (array) $leave,
            (array) $fresh
        );

        return response()->json([
            'status' => 'success',
            'message' => $status === 'approved' ? 'Leave approved successfully.' : 'Leave rejected successfully.',
            'data' => $fresh,
        ]);
    }

    private function activeEmployeesBaseQuery()
    {
        return DB::table('employee_profiles as ep')
            ->join('users as u', 'u.id', '=', 'ep.user_id')
            ->whereNull('ep.deleted_at')
            ->whereNull('u.deleted_at')
            ->where('ep.status', 'active')
            ->where('u.status', 'active');
    }

    private function attendanceBaseQuery()
    {
        return DB::table('employee_attendance as ea')
            ->join('users as u', 'u.id', '=', 'ea.user_id')
            ->leftJoin('employee_profiles as ep', 'ep.id', '=', 'ea.employee_profile_id')
            ->leftJoin('departments as d', 'd.id', '=', 'ep.department_id')
            ->leftJoin('designations as dg', 'dg.id', '=', 'ep.designation_id')
            ->leftJoin('branches as b', 'b.id', '=', 'ep.branch_id')
            ->leftJoin('shifts as s', 's.id', '=', 'ea.shift_id')
            ->whereNull('ea.deleted_at')
            ->select([
                'ea.*',
                'u.name',
                'u.email',
                'u.phone_number',
                'ep.employee_code',
                'd.name as department_name',
                'dg.name as designation_name',
                'b.name as branch_name',
                's.name as shift_name',
            ]);
    }

    private function applyEmployeeFilters($query, Request $request): void
    {
        foreach ([
            'department_id' => 'ep.department_id',
            'designation_id' => 'ep.designation_id',
            'branch_id' => 'ep.branch_id',
            'shift_id' => 'ep.shift_id',
        ] as $input => $column) {
            if ($request->filled($input)) {
                $query->where($column, (int) $request->query($input));
            }
        }
    }

    private function applyAttendanceFilters($query, Request $request): void
    {
        foreach ([
            'status' => 'ea.status',
            'approval_status' => 'ea.approval_status',
            'sync_status' => 'ea.sync_status',
            'attendance_mode' => 'ea.attendance_mode',
            'work_mode' => 'ea.work_mode',
        ] as $input => $column) {
            if ($request->filled($input)) {
                $query->where($column, $request->query($input));
            }
        }
    }

    private function resolveDateRange(Request $request): array
    {
        if ($request->filled('month')) {
            $month = Carbon::createFromFormat('Y-m', (string) $request->query('month'))->startOfMonth();
            return [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()];
        }

        $fromDate = $request->query('from') ?: $request->query('date');
        $toDate = $request->query('to') ?: $request->query('date');

        return [$fromDate, $toDate];
    }
}
