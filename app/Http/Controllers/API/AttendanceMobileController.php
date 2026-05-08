<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithAttendance;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceMobileController extends Controller
{
    use InteractsWithAttendance;

    public function bootstrap(Request $request)
    {
        $actor = $this->attendanceActor($request);
        $employee = $this->employeeContextByUserId($actor['id']);

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee profile not found'], 404);
        }

        $company = $this->companySettings();
        $branchNetworks = !empty($employee->branch_id)
            ? DB::table('branch_allowed_networks')
                ->where('branch_id', $employee->branch_id)
                ->where('is_active', 1)
                ->whereNull('deleted_at')
                ->get(['id', 'label', 'ip_pattern', 'network_type'])
            : collect();

        return response()->json([
            'status' => 'success',
            'data' => [
                'company' => $company,
                'employee' => $employee,
                'request_ip' => $request->ip(),
                'branch_networks' => $branchNetworks,
                'today_attendance' => $this->currentAttendanceForUser($actor['id']),
                'server_time' => now()->toIso8601String(),
                'status_options' => [
                    'attendance_statuses' => [
                        'present', 'absent', 'late', 'half_day', 'leave', 'holiday', 'week_off',
                        'pending_sync', 'pending_approval', 'rejected', 'manual_corrected',
                    ],
                    'sync_statuses' => [
                        'synced', 'sync_failed', 'offline_pending', 'duplicate_rejected',
                    ],
                ],
            ],
        ]);
    }

    public function requestIp(Request $request)
    {
        $ip = $request->ip();
        return response()->json([
            'status' => 'success',
            'data' => [
                'request_ip' => $ip,
                'is_loopback' => in_array($ip, ['127.0.0.1', '::1'], true),
                'hint' => in_array($ip, ['127.0.0.1', '::1'], true)
                    ? 'Localhost detected. Add 127.0.0.1 or ::1 to allowed IPs for local testing.'
                    : null,
                'server_time' => now()->toIso8601String(),
            ],
        ]);
    }

    public function activeSession(Request $request)
    {
        $actor = $this->attendanceActor($request);
        $record = $this->currentAttendanceForUser($actor['id']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'attendance' => $record,
                'tracks' => $record
                    ? DB::table('attendance_location_tracks')
                        ->where('employee_attendance_id', $record->id)
                        ->whereNull('deleted_at')
                        ->orderByDesc('recorded_at')
                        ->limit(20)
                        ->get()
                    : [],
            ],
        ]);
    }

    public function history(Request $request)
    {
        $actor = $this->attendanceActor($request);
        $perPage = max(1, min(100, (int) $request->query('per_page', 20)));

        $query = DB::table('employee_attendance')
            ->where('user_id', $actor['id'])
            ->whereNull('deleted_at');

        if ($request->filled('from')) {
            $query->whereDate('attendance_date', '>=', $request->query('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('attendance_date', '<=', $request->query('to'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $paginator = $query->orderByDesc('attendance_date')->paginate($perPage);

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

    public function punch(Request $request)
    {
        $validated = $request->validate($this->punchRules());
        $result = $this->processPunchPayload($request, $validated, false, null);

        return response()->json($result, $result['http_code'] ?? 200);
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1|max:200',
            'items.*.punch_type' => 'required|string|in:check_in,check_out',
            'items.*.attendance_mode' => 'sometimes|nullable|string|in:online,offline,manual',
            'items.*.work_mode' => 'sometimes|nullable|string|in:office,field,wfh,hybrid',
            'items.*.occurred_at' => 'sometimes|nullable|date',
            'items.*.latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'items.*.longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'items.*.gps_accuracy_meters' => 'sometimes|nullable|integer|min:0|max:5000',
            'items.*.location_text' => 'sometimes|nullable|string|max:255',
            'items.*.selfie_path' => 'sometimes|nullable|string|max:255',
            'items.*.face_match_score' => 'sometimes|nullable|numeric|min:0|max:100',
            'items.*.device_id' => 'sometimes|nullable|string|max:120',
            'items.*.device_name' => 'sometimes|nullable|string|max:150',
            'items.*.device_platform' => 'sometimes|nullable|string|max:40',
            'items.*.device_model' => 'sometimes|nullable|string|max:120',
            'items.*.os_version' => 'sometimes|nullable|string|max:60',
            'items.*.app_version' => 'sometimes|nullable|string|max:60',
            'items.*.battery_level' => 'sometimes|nullable|integer|min:0|max:100',
            'items.*.network_type' => 'sometimes|nullable|string|max:40',
            'items.*.internet_status' => 'sometimes|nullable|string|max:40',
            'items.*.local_queue_id' => 'sometimes|nullable|string|max:120',
            'items.*.remarks' => 'sometimes|nullable|string',
            'items.*.metadata' => 'sometimes|nullable|array',
        ]);

        $results = [];
        $synced = 0;
        $failed = 0;

        foreach ($validated['items'] as $item) {
            $queueId = DB::table('attendance_sync_queue')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'user_id' => $request->attributes->get('auth_user_id'),
                'local_queue_id' => $item['local_queue_id'] ?? null,
                'queue_type' => 'attendance_punch',
                'attendance_mode' => $item['attendance_mode'] ?? 'offline',
                'device_id' => $item['device_id'] ?? null,
                'payload' => json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'queued_at' => $this->parseClientTimestamp($item['occurred_at'] ?? null) ?? now(),
                'sync_status' => 'processing',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $result = $this->processPunchPayload($request, $item, true, $queueId);
            $results[] = $result;

            if (($result['status'] ?? 'error') === 'success') {
                $synced++;
            } else {
                $failed++;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Sync processed.',
            'data' => $results,
            'summary' => [
                'synced' => $synced,
                'failed' => $failed,
                'total' => count($results),
            ],
        ]);
    }

    public function locationPing(Request $request)
    {
        $validated = $request->validate([
            'attendance_id' => 'sometimes|nullable|integer|exists:employee_attendance,id',
            'recorded_at' => 'sometimes|nullable|date',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'gps_accuracy_meters' => 'sometimes|nullable|integer|min:0|max:5000',
            'battery_level' => 'sometimes|nullable|integer|min:0|max:100',
            'network_type' => 'sometimes|nullable|string|max:40',
            'is_background' => 'sometimes|nullable|boolean',
            'speed_kmph' => 'sometimes|nullable|numeric|min:0|max:500',
            'source' => 'sometimes|nullable|string|max:40',
            'sync_status' => 'sometimes|nullable|string|max:40',
            'local_queue_id' => 'sometimes|nullable|string|max:120',
            'metadata' => 'sometimes|nullable|array',
        ]);

        $actor = $this->attendanceActor($request);
        $attendance = null;

        if (!empty($validated['attendance_id'])) {
            $attendance = DB::table('employee_attendance')
                ->where('id', $validated['attendance_id'])
                ->where('user_id', $actor['id'])
                ->whereNull('deleted_at')
                ->first();
        }

        if (!$attendance) {
            $attendance = $this->currentAttendanceForUser($actor['id']);
        }

        if (!$attendance || empty($attendance->check_in_time) || !empty($attendance->check_out_time)) {
            return response()->json(['status' => 'error', 'message' => 'No active attendance session found'], 422);
        }

        $id = DB::table('attendance_location_tracks')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'employee_attendance_id' => $attendance->id,
            'user_id' => $actor['id'],
            'recorded_at' => $this->parseClientTimestamp($validated['recorded_at'] ?? null) ?? now(),
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'gps_accuracy_meters' => $validated['gps_accuracy_meters'] ?? null,
            'battery_level' => $validated['battery_level'] ?? null,
            'network_type' => $validated['network_type'] ?? null,
            'is_background' => $validated['is_background'] ?? null,
            'speed_kmph' => $validated['speed_kmph'] ?? null,
            'source' => $validated['source'] ?? 'manual_ping',
            'sync_status' => $validated['sync_status'] ?? 'synced',
            'local_queue_id' => $validated['local_queue_id'] ?? null,
            'metadata' => !empty($validated['metadata']) ? json_encode($validated['metadata'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Location ping recorded.',
            'data' => DB::table('attendance_location_tracks')->where('id', $id)->first(),
        ], 201);
    }

    public function syncQueue(Request $request)
    {
        $actor = $this->attendanceActor($request);

        $rows = DB::table('attendance_sync_queue')
            ->where('user_id', $actor['id'])
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return response()->json(['status' => 'success', 'data' => $rows]);
    }

    public function myLeaves(Request $request)
    {
        $actor = $this->attendanceActor($request);

        $rows = DB::table('leave_requests as lr')
            ->leftJoin('leave_types as lt', 'lt.id', '=', 'lr.leave_type_id')
            ->where('lr.user_id', $actor['id'])
            ->whereNull('lr.deleted_at')
            ->select('lr.*', 'lt.name as leave_type_name')
            ->orderByDesc('lr.id')
            ->paginate(max(1, min(100, (int) $request->query('per_page', 20))));

        return response()->json([
            'status' => 'success',
            'data' => $rows->items(),
            'pagination' => [
                'page' => $rows->currentPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
                'last_page' => $rows->lastPage(),
            ],
        ]);
    }

    public function summary(Request $request)
    {
        $actor    = $this->attendanceActor($request);
        $employee = $this->employeeContextByUserId($actor['id']);

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee profile not found'], 404);
        }

        $month = $request->query('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);
        $from = Carbon::createFromDate((int) $year, (int) $mon, 1)->startOfMonth();
        $to   = $from->copy()->endOfMonth();

        $summary = DB::table('employee_attendance')
            ->where('user_id', $actor['id'])
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
            ->whereNull('deleted_at')
            ->selectRaw("
                COUNT(*) as total_marked,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_days,
                SUM(CASE WHEN status = 'half_day' THEN 1 ELSE 0 END) as half_days,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'leave' THEN 1 ELSE 0 END) as leave_days,
                SUM(CASE WHEN status = 'holiday' THEN 1 ELSE 0 END) as holiday_days,
                SUM(CASE WHEN status = 'week_off' THEN 1 ELSE 0 END) as week_off_days,
                SUM(COALESCE(total_working_minutes, 0)) as total_working_minutes,
                SUM(COALESCE(overtime_minutes, 0)) as total_overtime_minutes,
                SUM(COALESCE(late_minutes, 0)) as total_late_minutes,
                SUM(CASE WHEN approval_status = 'pending_approval' THEN 1 ELSE 0 END) as pending_approvals
            ")
            ->first();

        $leaveSummary = DB::table('leave_requests')
            ->where('user_id', $actor['id'])
            ->whereNull('deleted_at')
            ->selectRaw("
                SUM(CASE WHEN status = 'pending_approval' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            ")
            ->first();

        $pendingSync = DB::table('attendance_sync_queue')
            ->where('user_id', $actor['id'])
            ->whereIn('sync_status', ['processing', 'pending_approval', 'sync_failed'])
            ->whereNull('deleted_at')
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'month' => $month,
                'attendance' => [
                    'total_marked'         => (int) ($summary->total_marked ?? 0),
                    'present_days'         => (int) ($summary->present_days ?? 0),
                    'late_days'            => (int) ($summary->late_days ?? 0),
                    'half_days'            => (int) ($summary->half_days ?? 0),
                    'absent_days'          => (int) ($summary->absent_days ?? 0),
                    'leave_days'           => (int) ($summary->leave_days ?? 0),
                    'holiday_days'         => (int) ($summary->holiday_days ?? 0),
                    'week_off_days'        => (int) ($summary->week_off_days ?? 0),
                    'total_working_minutes'=> (int) ($summary->total_working_minutes ?? 0),
                    'total_overtime_minutes'=> (int) ($summary->total_overtime_minutes ?? 0),
                    'total_late_minutes'   => (int) ($summary->total_late_minutes ?? 0),
                    'pending_approvals'    => (int) ($summary->pending_approvals ?? 0),
                ],
                'leaves' => [
                    'pending'  => (int) ($leaveSummary->pending ?? 0),
                    'approved' => (int) ($leaveSummary->approved ?? 0),
                    'rejected' => (int) ($leaveSummary->rejected ?? 0),
                ],
                'pending_sync' => $pendingSync,
            ],
        ]);
    }

    public function applyLeave(Request $request)
    {
        $validated = $request->validate([
            'leave_type_id' => 'sometimes|nullable|integer|exists:leave_types,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'total_days' => 'sometimes|nullable|numeric|min:0|max:365',
            'reason' => 'required|string',
            'metadata' => 'sometimes|nullable|array',
        ]);

        $actor = $this->attendanceActor($request);
        $employee = $this->employeeContextByUserId($actor['id']);

        $id = DB::table('leave_requests')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'user_id' => $actor['id'],
            'employee_profile_id' => $employee?->employee_profile_id,
            'leave_type_id' => $validated['leave_type_id'] ?? null,
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'total_days' => $validated['total_days'] ?? Carbon::parse($validated['from_date'])->diffInDays(Carbon::parse($validated['to_date'])) + 1,
            'reason' => $validated['reason'],
            'status' => 'pending_approval',
            'applied_at' => now(),
            'metadata' => !empty($validated['metadata']) ? json_encode($validated['metadata'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->createAttendanceAudit($request, 'leave_requests', 'create', 'leave_requests', $id, 'Employee applied for leave.', [], $validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Leave request submitted successfully.',
            'data' => DB::table('leave_requests')->where('id', $id)->first(),
        ], 201);
    }

    private function processPunchPayload(Request $request, array $payload, bool $fromSync, ?int $queueId): array
    {
        $actor = $this->attendanceActor($request);
        $employee = $this->employeeContextByUserId($actor['id']);

        if (!$employee) {
            return $this->markQueueAndRespond($queueId, [
                'status' => 'error',
                'message' => 'Employee profile not found',
                'http_code' => 404,
            ]);
        }

        $punchType = strtolower((string) ($payload['punch_type'] ?? ''));
        $attendanceMode = strtolower((string) ($payload['attendance_mode'] ?? ($fromSync ? 'offline' : 'online')));
        $internetStatus = strtolower((string) ($payload['internet_status'] ?? $attendanceMode));
        $workMode = strtolower((string) ($payload['work_mode'] ?? ($employee->work_mode ?: 'office')));
        $occurredAt = $this->parseClientTimestamp($payload['occurred_at'] ?? null) ?? now();
        $latitude = isset($payload['latitude']) ? (float) $payload['latitude'] : null;
        $longitude = isset($payload['longitude']) ? (float) $payload['longitude'] : null;
        $locationText = $payload['location_text'] ?? $this->coordinateLabel($latitude, $longitude, $payload['gps_accuracy_meters'] ?? null);
        $localQueueId = $payload['local_queue_id'] ?? null;
        $openRecord = $this->latestOpenAttendanceForUser($actor['id']);
        $existingRecord = $this->resolveAttendanceRecordForPunch($actor['id'], $punchType, $occurredAt, $openRecord);
        $attendanceDate = $existingRecord?->attendance_date ?? $occurredAt->toDateString();

        if ($localQueueId) {
            $existing = DB::table('attendance_logs')
                ->where('user_id', $actor['id'])
                ->where('local_queue_id', $localQueueId)
                ->whereNull('deleted_at')
                ->first();

            if ($existing) {
                return $this->markQueueAndRespond($queueId, [
                    'status' => 'success',
                    'message' => 'Attendance already synced.',
                    'data' => [
                        'attendance_log_id' => $existing->id,
                        'employee_attendance_id' => $existing->employee_attendance_id,
                        'sync_status' => 'duplicate_rejected',
                    ],
                ], 'duplicate_rejected');
            }
        }

        if ($punchType === 'check_in' && $openRecord && !filter_var($employee->multiple_punch_allowed, FILTER_VALIDATE_BOOLEAN)) {
            return $this->markQueueAndRespond($queueId, [
                'status' => 'error',
                'message' => 'An active attendance session is already open.',
                'http_code' => 422,
            ], 'duplicate_rejected');
        }

        if ($punchType === 'check_in' && $existingRecord && !empty($existingRecord->check_in_time) && !filter_var($employee->multiple_punch_allowed, FILTER_VALIDATE_BOOLEAN)) {
            return $this->markQueueAndRespond($queueId, [
                'status' => 'error',
                'message' => 'Duplicate check-in is not allowed.',
                'http_code' => 422,
            ], 'duplicate_rejected');
        }

        if ($punchType === 'check_out' && (!$existingRecord || empty($existingRecord->check_in_time))) {
            return $this->markQueueAndRespond($queueId, [
                'status' => 'error',
                'message' => 'Check-in is required before check-out.',
                'http_code' => 422,
            ], 'sync_failed');
        }

        if ($punchType === 'check_out' && !empty($existingRecord->check_out_time) && !filter_var($employee->multiple_punch_allowed, FILTER_VALIDATE_BOOLEAN)) {
            return $this->markQueueAndRespond($queueId, [
                'status' => 'error',
                'message' => 'Duplicate check-out is not allowed.',
                'http_code' => 422,
            ], 'duplicate_rejected');
        }

        $selfiePath = $payload['selfie_path'] ?? null;
        if (!$selfiePath && $request->hasFile('selfie')) {
            $saved = $this->storePublicMedia($request->file('selfie'), 'assets/media/images/attendance/selfies', 'attendance');
            if ($saved !== false) {
                $selfiePath = $saved;
            }
        }

        $networkCheck = $this->evaluateNetwork($request, $employee->branch_id ? (int) $employee->branch_id : null, $employee, (string) ($payload['network_type'] ?? ''), $workMode);
        $geofenceCheck = $this->evaluateGeofence($employee, $latitude, $longitude, $workMode);
        $deviceCheck = $this->ensureDeviceRegistration(
            $actor['id'],
            $employee->employee_profile_id ? (int) $employee->employee_profile_id : null,
            $payload['device_id'] ?? null,
            [
                'device_name' => $payload['device_name'] ?? null,
                'device_platform' => $payload['device_platform'] ?? null,
                'device_model' => $payload['device_model'] ?? null,
                'os_version' => $payload['os_version'] ?? null,
                'app_version' => $payload['app_version'] ?? null,
                'last_ip' => $request->ip(),
                'metadata' => $payload['metadata'] ?? [],
            ],
            filter_var($employee->device_binding_required, FILTER_VALIDATE_BOOLEAN)
        );

        $exceptions = $this->evaluatePunchExceptions(
            $employee,
            $punchType,
            $attendanceMode,
            $internetStatus,
            $workMode,
            $occurredAt,
            $latitude,
            $longitude,
            $selfiePath,
            $payload,
            $networkCheck,
            $geofenceCheck,
            $deviceCheck
        );

        $approvalStatus = empty($exceptions)
            ? (filter_var($employee->auto_approve_clean_records ?? true, FILTER_VALIDATE_BOOLEAN) !== false ? 'approved' : 'pending_approval')
            : 'pending_approval';

        DB::beginTransaction();

        try {
            $recordId = $existingRecord?->id;

            if (!$recordId) {
                $recordId = DB::table('employee_attendance')->insertGetId([
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $actor['id'],
                    'employee_profile_id' => $employee->employee_profile_id,
                    'branch_id' => $employee->branch_id,
                    'shift_id' => $employee->shift_id,
                    'attendance_policy_id' => $employee->attendance_policy_id,
                    'attendance_date' => $attendanceDate,
                    'attendance_mode' => $attendanceMode,
                    'work_mode' => $workMode,
                    'request_ip' => $request->ip(),
                    'network_type' => $payload['network_type'] ?? null,
                    'wifi_ip_match' => $networkCheck['matched_pattern'] ?? null,
                    'within_geofence' => $geofenceCheck['inside'],
                    'within_wifi_ip' => $networkCheck['matched'],
                    'sync_status' => 'synced',
                    'approval_status' => $approvalStatus,
                    'status' => $approvalStatus === 'pending_approval' ? 'pending_approval' : null,
                    'remarks' => $payload['remarks'] ?? null,
                    'metadata' => !empty($payload['metadata']) ? json_encode($payload['metadata'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $logId = DB::table('attendance_logs')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'employee_attendance_id' => $recordId,
                'user_id' => $actor['id'],
                'employee_profile_id' => $employee->employee_profile_id,
                'branch_id' => $employee->branch_id,
                'shift_id' => $employee->shift_id,
                'attendance_policy_id' => $employee->attendance_policy_id,
                'punch_type' => $punchType,
                'punch_time' => $occurredAt,
                'server_received_at' => now(),
                'attendance_mode' => $attendanceMode,
                'work_mode' => $workMode,
                'internet_status' => $internetStatus,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'gps_accuracy_meters' => $payload['gps_accuracy_meters'] ?? null,
                'location_text' => $locationText,
                'selfie_path' => $selfiePath,
                'face_match_score' => $payload['face_match_score'] ?? null,
                'device_id' => $payload['device_id'] ?? null,
                'device_name' => $payload['device_name'] ?? null,
                'device_platform' => $payload['device_platform'] ?? null,
                'app_version' => $payload['app_version'] ?? null,
                'battery_level' => $payload['battery_level'] ?? null,
                'network_type' => $payload['network_type'] ?? null,
                'request_ip' => $request->ip(),
                'local_queue_id' => $localQueueId,
                'sync_status' => 'synced',
                'synced_at' => now(),
                'within_geofence' => $geofenceCheck['inside'],
                'within_wifi_ip' => $networkCheck['matched'],
                'is_exception' => !empty($exceptions),
                'exception_reason' => !empty($exceptions) ? implode(',', $exceptions) : null,
                'metadata' => !empty($payload['metadata']) ? json_encode($payload['metadata'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $updates = [
                'attendance_mode' => $attendanceMode,
                'work_mode' => $workMode,
                'request_ip' => $request->ip(),
                'network_type' => $payload['network_type'] ?? null,
                'wifi_ip_match' => $networkCheck['matched_pattern'] ?? null,
                'within_geofence' => $geofenceCheck['inside'],
                'within_wifi_ip' => $networkCheck['matched'],
                'approval_status' => $approvalStatus,
                'sync_status' => 'synced',
                'device_id' => $payload['device_id'] ?? null,
                'remarks' => $payload['remarks'] ?? ($existingRecord->remarks ?? null),
                'approved_at' => $approvalStatus === 'approved' ? now() : null,
                'updated_at' => now(),
            ];

            if ($punchType === 'check_in') {
                if (empty($existingRecord?->check_in_time) || Carbon::parse($existingRecord->check_in_time)->greaterThan($occurredAt)) {
                    $updates['check_in_time'] = $occurredAt;
                    $updates['check_in_latitude'] = $latitude;
                    $updates['check_in_longitude'] = $longitude;
                    $updates['check_in_selfie_path'] = $selfiePath;
                }
            }

            if ($punchType === 'check_out') {
                $updates['check_out_time'] = $occurredAt;
                $updates['check_out_latitude'] = $latitude;
                $updates['check_out_longitude'] = $longitude;
                $updates['check_out_selfie_path'] = $selfiePath;
            }

            DB::table('employee_attendance')->where('id', $recordId)->update($updates);
            $finalRecord = $this->recalculateAttendanceRecord((int) $recordId, $employee, $approvalStatus);

            if (!empty($exceptions)) {
                $this->createAttendanceApproval([
                    'user_id' => $actor['id'],
                    'employee_attendance_id' => $recordId,
                    'attendance_log_id' => $logId,
                    'requested_by' => $actor['id'],
                    'approval_type' => $exceptions[0],
                    'status' => 'pending_approval',
                    'reason' => 'Attendance requires HR review.',
                    'snapshot' => [
                        'exceptions' => $exceptions,
                        'request_ip' => $request->ip(),
                        'network_check' => $networkCheck,
                        'geofence_check' => $geofenceCheck,
                    ],
                ]);
            }

            DB::commit();

            $response = [
                'status' => 'success',
                'message' => !empty($exceptions)
                    ? 'Attendance captured and sent for approval.'
                    : 'Attendance captured successfully.',
                'data' => [
                    'employee_attendance_id' => $recordId,
                    'attendance_log_id' => $logId,
                    'approval_status' => $approvalStatus,
                    'exceptions' => $exceptions,
                    'attendance' => $finalRecord,
                ],
            ];

            return $this->markQueueAndRespond($queueId, $response, empty($exceptions) ? 'synced' : 'pending_approval', $recordId);
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->markQueueAndRespond($queueId, [
                'status' => 'error',
                'message' => 'Could not save attendance.',
                'error' => $e->getMessage(),
                'http_code' => 500,
            ], 'sync_failed');
        }
    }

    private function evaluatePunchExceptions(
        object $employee,
        string $punchType,
        string $attendanceMode,
        string $internetStatus,
        string $workMode,
        Carbon $occurredAt,
        ?float $latitude,
        ?float $longitude,
        ?string $selfiePath,
        array $payload,
        array $networkCheck,
        array $geofenceCheck,
        array $deviceCheck
    ): array {
        $exceptions = [];

        if (filter_var($employee->gps_required, FILTER_VALIDATE_BOOLEAN) && ($latitude === null || $longitude === null)) {
            $exceptions[] = 'gps_missing';
        } elseif (($latitude === null || $longitude === null) && filter_var($employee->mark_gps_missing_as_exception, FILTER_VALIDATE_BOOLEAN)) {
            $exceptions[] = 'gps_exception';
        }

        if ($geofenceCheck['required'] && $geofenceCheck['inside'] !== true) {
            $exceptions[] = $geofenceCheck['reason'] ?: 'outside_geofence';
        }

        if (($attendanceMode === 'offline' || $internetStatus === 'offline') && $networkCheck['required']) {
            $exceptions[] = 'offline_wifi_verification_required';
        }

        if ($networkCheck['required'] && $networkCheck['matched'] !== true) {
            $exceptions[] = $networkCheck['reason'] ?: 'wifi_ip_not_allowed';
        }

        if (!$deviceCheck['valid']) {
            $exceptions[] = $deviceCheck['reason'] ?: 'device_not_bound';
        }

        $selfieRequired = filter_var($employee->selfie_required, FILTER_VALIDATE_BOOLEAN);
        if ($punchType === 'check_out') {
            $selfieRequired = filter_var($employee->checkout_selfie_required, FILTER_VALIDATE_BOOLEAN) || $selfieRequired;
        }

        if ($selfieRequired && !$selfiePath) {
            $exceptions[] = 'selfie_missing';
        }

        if ($attendanceMode === 'offline' || $internetStatus === 'offline') {
            if (filter_var($employee->offline_attendance_allowed, FILTER_VALIDATE_BOOLEAN) === false) {
                $exceptions[] = 'offline_not_allowed';
            }

            $limitMinutes = null;
            if (!empty($employee->offline_sync_limit_minutes)) {
                $limitMinutes = (int) $employee->offline_sync_limit_minutes;
            } elseif (!empty($employee->offline_sync_limit_hours)) {
                $limitMinutes = (int) $employee->offline_sync_limit_hours * 60;
            }

            if ($limitMinutes !== null && $occurredAt->diffInMinutes(now()) > $limitMinutes) {
                $exceptions[] = 'offline_sync_delay';
            }
            if (!empty($employee->max_offline_records_per_day)) {
                $offlineCount = DB::table('attendance_logs')
                    ->where('user_id', $employee->user_id)
                    ->whereDate('punch_time', $occurredAt->toDateString())
                    ->where('attendance_mode', 'offline')
                    ->whereNull('deleted_at')
                    ->count();

                if ($offlineCount >= (int) $employee->max_offline_records_per_day) {
                    $exceptions[] = 'offline_daily_limit_exceeded';
                }
            }
        } elseif (!empty($employee->time_drift_tolerance_minutes) && $occurredAt->diffInMinutes(now()) > (int) $employee->time_drift_tolerance_minutes) {
            $exceptions[] = 'device_time_drift';
        }

        if ($workMode === 'field' && filter_var($employee->allow_field_attendance, FILTER_VALIDATE_BOOLEAN) === false) {
            $exceptions[] = 'field_attendance_not_allowed';
        }

        if ($workMode === 'wfh' && filter_var($employee->allow_wfh_attendance, FILTER_VALIDATE_BOOLEAN) === false) {
            $exceptions[] = 'wfh_attendance_not_allowed';
        }

        if ($workMode === 'wfh' && filter_var($employee->require_work_note_for_wfh, FILTER_VALIDATE_BOOLEAN) && empty($payload['remarks'])) {
            $exceptions[] = 'wfh_note_required';
        }

        return array_values(array_unique($exceptions));
    }

    private function markQueueAndRespond(?int $queueId, array $response, ?string $syncStatus = null, ?int $attendanceId = null): array
    {
        if ($queueId) {
            DB::table('attendance_sync_queue')->where('id', $queueId)->update([
                'employee_attendance_id' => $attendanceId,
                'sync_status' => $syncStatus ?? (($response['status'] ?? 'error') === 'success' ? 'synced' : 'sync_failed'),
                'synced_at' => ($response['status'] ?? 'error') === 'success' ? now() : null,
                'attempts' => DB::raw('attempts + 1'),
                'last_error' => ($response['status'] ?? 'error') === 'error' ? ($response['message'] ?? 'Sync failed') : null,
                'last_response' => json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }

        return $response;
    }

    private function todayAttendanceForUser(int $userId): ?object
    {
        return DB::table('employee_attendance')
            ->where('user_id', $userId)
            ->whereDate('attendance_date', now()->toDateString())
            ->whereNull('deleted_at')
            ->first();
    }

    private function currentAttendanceForUser(int $userId): ?object
    {
        $record = $this->latestOpenAttendanceForUser($userId) ?: $this->todayAttendanceForUser($userId);

        return $record ? $this->decorateAttendanceRecord($record) : null;
    }

    private function latestOpenAttendanceForUser(int $userId): ?object
    {
        return DB::table('employee_attendance')
            ->where('user_id', $userId)
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->whereNull('deleted_at')
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->first();
    }

    private function resolveAttendanceRecordForPunch(int $userId, string $punchType, Carbon $occurredAt, ?object $openRecord = null): ?object
    {
        if ($punchType === 'check_out' && $openRecord) {
            return $openRecord;
        }

        return DB::table('employee_attendance')
            ->where('user_id', $userId)
            ->whereDate('attendance_date', $occurredAt->toDateString())
            ->whereNull('deleted_at')
            ->first();
    }

    private function punchRules(): array
    {
        return [
            'punch_type' => 'required|string|in:check_in,check_out',
            'attendance_mode' => 'sometimes|nullable|string|in:online,offline,manual',
            'work_mode' => 'sometimes|nullable|string|in:office,field,wfh,hybrid',
            'occurred_at' => 'sometimes|nullable|date',
            'latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'gps_accuracy_meters' => 'sometimes|nullable|integer|min:0|max:5000',
            'location_text' => 'sometimes|nullable|string|max:255',
            'selfie' => 'sometimes|file|mimes:jpg,jpeg,png,webp|max:5120',
            'selfie_path' => 'sometimes|nullable|string|max:255',
            'face_match_score' => 'sometimes|nullable|numeric|min:0|max:100',
            'device_id' => 'sometimes|nullable|string|max:120',
            'device_name' => 'sometimes|nullable|string|max:150',
            'device_platform' => 'sometimes|nullable|string|max:40',
            'device_model' => 'sometimes|nullable|string|max:120',
            'os_version' => 'sometimes|nullable|string|max:60',
            'app_version' => 'sometimes|nullable|string|max:60',
            'battery_level' => 'sometimes|nullable|integer|min:0|max:100',
            'network_type' => 'sometimes|nullable|string|max:40',
            'internet_status' => 'sometimes|nullable|string|max:40',
            'local_queue_id' => 'sometimes|nullable|string|max:120',
            'remarks' => 'sometimes|nullable|string',
            'metadata' => 'sometimes|nullable|array',
        ];
    }

    private function decorateAttendanceRecord(object $record): object
    {
        $latestLog = DB::table('attendance_logs')
            ->where('employee_attendance_id', $record->id)
            ->whereNull('deleted_at')
            ->orderByDesc('punch_time')
            ->orderByDesc('id')
            ->select([
                'id',
                'punch_type',
                'punch_time',
                'attendance_mode',
                'work_mode',
                'internet_status',
                'network_type',
                'request_ip',
                'location_text',
                'latitude',
                'longitude',
                'gps_accuracy_meters',
                'sync_status',
                'exception_reason',
            ])
            ->first();

        $latestTrack = DB::table('attendance_location_tracks')
            ->where('employee_attendance_id', $record->id)
            ->whereNull('deleted_at')
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->select([
                'id',
                'recorded_at',
                'latitude',
                'longitude',
                'gps_accuracy_meters',
                'network_type',
                'sync_status',
                'source',
            ])
            ->first();

        $record->latest_log = $latestLog;
        $record->latest_track = $latestTrack;
        $record->display_location = $latestLog?->location_text
            ?: $this->coordinateLabel(
                $latestLog?->latitude !== null ? (float) $latestLog->latitude : ($latestTrack?->latitude !== null ? (float) $latestTrack->latitude : null),
                $latestLog?->longitude !== null ? (float) $latestLog->longitude : ($latestTrack?->longitude !== null ? (float) $latestTrack->longitude : null),
                $latestLog?->gps_accuracy_meters ?? $latestTrack?->gps_accuracy_meters ?? null
            );

        return $record;
    }

    private function coordinateLabel(?float $latitude, ?float $longitude, $accuracyMeters = null): ?string
    {
        if ($latitude === null || $longitude === null) {
            return null;
        }

        $label = number_format($latitude, 5, '.', '') . ', ' . number_format($longitude, 5, '.', '');
        if ($accuracyMeters !== null && $accuracyMeters !== '') {
            $label .= ' (±' . (int) $accuracyMeters . 'm)';
        }

        return $label;
    }
}
