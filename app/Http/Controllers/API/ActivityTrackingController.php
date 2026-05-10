<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithAttendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivityTrackingController extends Controller
{
    use InteractsWithAttendance;

    /* ═══════════════════════════════════════════════════════════════
       POST /api/attendance/mobile/activity-log
       Employee submits (or updates) their daily activity log.
       Upserts on (user_id + attendance_date). Safe to call multiple
       times — latest payload wins so the app can sync incrementally.
    ═══════════════════════════════════════════════════════════════ */
    public function logActivity(Request $request)
    {
        $actor  = $this->attendanceActor($request);
        $userId = $actor['id'];
        $date   = $request->input('attendance_date', now()->toDateString());

        // Resolve linked attendance record
        $attendance = DB::table('employee_attendance')
            ->where('user_id', $userId)
            ->where('attendance_date', $date)
            ->whereNull('deleted_at')
            ->first();

        // Resolve employee profile
        $employee = DB::table('employee_profiles')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->first();

        // Decode JSON fields that might arrive as strings
        $gpsPath      = $this->decodeJsonField($request->input('gps_path'));
        $wifiNetworks = $this->decodeJsonField($request->input('wifi_networks_seen'));
        $rawEvents    = $this->decodeJsonField($request->input('raw_events'));
        $metadata     = $this->decodeJsonField($request->input('metadata'));

        $payload = [
            'user_id'                              => $userId,
            'employee_profile_id'                  => $employee?->id,
            'attendance_id'                        => $attendance?->id,
            'attendance_date'                      => $date,
            'session_start'                        => $request->input('session_start'),
            'session_end'                          => $request->input('session_end'),
            'platform'                             => $request->input('platform'),
            'app_version'                          => $request->input('app_version'),
            'device_id'                            => $request->input('device_id'),
            // GPS
            'gps_connect_count'                    => (int) $request->input('gps_connect_count', 0),
            'gps_disconnect_count'                 => (int) $request->input('gps_disconnect_count', 0),
            'gps_path'                             => $gpsPath ? json_encode($gpsPath) : null,
            'total_distance_meters'                => (int) $request->input('total_distance_meters', 0),
            'is_traveling'                         => filter_var($request->input('is_traveling', false), FILTER_VALIDATE_BOOLEAN),
            'time_traveling_minutes'               => (int) $request->input('time_traveling_minutes', 0),
            'time_stationary_minutes'              => (int) $request->input('time_stationary_minutes', 0),
            'max_speed_kmh'                        => $request->input('max_speed_kmh') !== null ? (float) $request->input('max_speed_kmh') : null,
            'avg_speed_kmh'                        => $request->input('avg_speed_kmh') !== null ? (float) $request->input('avg_speed_kmh') : null,
            'furthest_distance_from_office_meters' => $request->input('furthest_distance_from_office_meters') !== null
                                                        ? (int) $request->input('furthest_distance_from_office_meters') : null,
            // WiFi
            'wifi_connect_count'                   => (int) $request->input('wifi_connect_count', 0),
            'wifi_disconnect_count'                => (int) $request->input('wifi_disconnect_count', 0),
            'wifi_switch_count'                    => (int) $request->input('wifi_switch_count', 0),
            'wifi_networks_seen'                   => $wifiNetworks ? json_encode($wifiNetworks) : null,
            // Office / Geofence
            'office_entry_count'                   => (int) $request->input('office_entry_count', 0),
            'office_exit_count'                    => (int) $request->input('office_exit_count', 0),
            'time_inside_office_minutes'           => (int) $request->input('time_inside_office_minutes', 0),
            'time_outside_office_minutes'          => (int) $request->input('time_outside_office_minutes', 0),
            // Movement
            'movement_event_count'                 => (int) $request->input('movement_event_count', 0),
            'idle_streak_max_minutes'              => (int) $request->input('idle_streak_max_minutes', 0),
            // App state
            'app_foreground_count'                 => (int) $request->input('app_foreground_count', 0),
            'app_background_count'                 => (int) $request->input('app_background_count', 0),
            'app_foreground_minutes'               => (int) $request->input('app_foreground_minutes', 0),
            'app_background_minutes'               => (int) $request->input('app_background_minutes', 0),
            // Battery
            'battery_start_percent'                => $request->input('battery_start_percent') !== null
                                                        ? (int) $request->input('battery_start_percent') : null,
            'battery_end_percent'                  => $request->input('battery_end_percent') !== null
                                                        ? (int) $request->input('battery_end_percent') : null,
            // Sync
            'client_created_at'                    => $request->input('client_created_at'),
            'raw_events'                           => $rawEvents ? json_encode($rawEvents) : null,
            'metadata'                             => $metadata  ? json_encode($metadata)  : null,
            'synced_at'                            => now(),
            'updated_at'                           => now(),
        ];

        $existing = DB::table('employee_activity_logs')
            ->where('user_id', $userId)
            ->where('attendance_date', $date)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            // Merge raw_events — append new events onto the existing ones (keep latest 500)
            if ($rawEvents) {
                $existingEvents = json_decode($existing->raw_events ?? '[]', true) ?: [];
                $merged         = array_merge($existingEvents, $rawEvents);
                if (count($merged) > 500) {
                    $merged = array_slice($merged, -500); // keep most recent 500
                }
                $payload['raw_events'] = json_encode(array_values($merged));
            }

            // For nullable fields that arrive as 0/null, only overwrite if incoming value is non-null/non-zero
            // so that a punch-only sync doesn't wipe out full-day counters from a previous sync.
            $numericFields = [
                'gps_connect_count','gps_disconnect_count','total_distance_meters',
                'time_traveling_minutes','time_stationary_minutes',
                'wifi_connect_count','wifi_disconnect_count','wifi_switch_count',
                'office_entry_count','office_exit_count',
                'time_inside_office_minutes','time_outside_office_minutes',
                'movement_event_count','idle_streak_max_minutes',
                'app_foreground_count','app_background_count',
                'app_foreground_minutes','app_background_minutes',
            ];
            foreach ($numericFields as $field) {
                if (isset($payload[$field]) && $payload[$field] == 0 && ($existing->$field ?? 0) > 0) {
                    $payload[$field] = $existing->$field; // keep the larger existing value
                }
            }

            // Keep existing GPS path if incoming is empty
            if (empty($rawEvents) && !$gpsPath && $existing->gps_path) {
                unset($payload['gps_path']);
            }

            // Keep existing wifi_networks_seen if incoming is empty
            if (!$wifiNetworks && $existing->wifi_networks_seen) {
                unset($payload['wifi_networks_seen']);
            }

            // Preserve battery_start from first sync; only update battery_end
            if ($payload['battery_start_percent'] === null && $existing->battery_start_percent !== null) {
                unset($payload['battery_start_percent']);
            }

            DB::table('employee_activity_logs')
                ->where('id', $existing->id)
                ->update($payload);
            $id = $existing->id;
        } else {
            $payload['uuid']       = (string) Str::uuid();
            $payload['created_at'] = now();
            $id = DB::table('employee_activity_logs')->insertGetId($payload);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Activity log saved.',
            'data'    => ['id' => $id, 'attendance_date' => $date],
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════
       GET /api/attendance/mobile/activity-log?date=YYYY-MM-DD
       Employee fetches their own activity log for a specific date.
    ═══════════════════════════════════════════════════════════════ */
    public function myActivityLog(Request $request)
    {
        $actor  = $this->attendanceActor($request);
        $userId = $actor['id'];
        $date   = $request->input('date', now()->toDateString());

        $log = DB::table('employee_activity_logs')
            ->where('user_id', $userId)
            ->where('attendance_date', $date)
            ->whereNull('deleted_at')
            ->first();

        return response()->json([
            'status' => 'success',
            'data'   => $log ? $this->formatLog($log) : null,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════
       GET /api/attendance/activity-logs   (admin / hr)
       Paginated list of all employees' activity logs.
    ═══════════════════════════════════════════════════════════════ */
    public function listActivityLogs(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 25), 100);
        $page    = max((int) $request->input('page', 1), 1);
        $date    = $request->input('date');
        $from    = $request->input('from');
        $to      = $request->input('to');
        $userId  = $request->input('user_id');
        $search  = $request->input('search');

        $q = DB::table('employee_activity_logs as al')
            ->join('users as u', 'u.id', '=', 'al.user_id')
            ->leftJoin('employee_profiles as ep', 'ep.id', '=', 'al.employee_profile_id')
            ->leftJoin('employee_attendance as ea', 'ea.id', '=', 'al.attendance_id')
            ->whereNull('al.deleted_at')
            ->select([
                'al.id', 'al.uuid', 'al.user_id', 'al.attendance_id', 'al.attendance_date',
                'al.platform', 'al.app_version',
                'al.session_start', 'al.session_end',
                // GPS
                'al.gps_connect_count', 'al.gps_disconnect_count',
                'al.total_distance_meters', 'al.is_traveling',
                'al.time_traveling_minutes', 'al.time_stationary_minutes',
                'al.max_speed_kmh', 'al.furthest_distance_from_office_meters',
                // WiFi
                'al.wifi_connect_count', 'al.wifi_disconnect_count', 'al.wifi_switch_count',
                // Office
                'al.office_entry_count', 'al.office_exit_count',
                'al.time_inside_office_minutes', 'al.time_outside_office_minutes',
                // Movement
                'al.movement_event_count', 'al.idle_streak_max_minutes',
                // App
                'al.app_foreground_minutes', 'al.app_background_minutes',
                // Battery
                'al.battery_start_percent', 'al.battery_end_percent',
                'al.synced_at',
                // Employee
                'u.name as employee_name',
                'ep.employee_code',
                // Attendance status
                'ea.status as attendance_status',
                'ea.check_in_time', 'ea.check_out_time',
            ]);

        if ($date)   $q->where('al.attendance_date', $date);
        if ($from)   $q->where('al.attendance_date', '>=', $from);
        if ($to)     $q->where('al.attendance_date', '<=', $to);
        if ($userId) $q->where('al.user_id', $userId);
        if ($search) {
            $q->where(function ($qb) use ($search) {
                $qb->where('u.name', 'like', "%$search%")
                   ->orWhere('ep.employee_code', 'like', "%$search%");
            });
        }

        $total = (clone $q)->count();
        $logs  = $q->orderByDesc('al.attendance_date')
                   ->orderBy('u.name')
                   ->offset(($page - 1) * $perPage)
                   ->limit($perPage)
                   ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $logs,
            'pagination' => [
                'total'     => $total,
                'per_page'  => $perPage,
                'page'      => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════
       GET /api/attendance/activity-logs/{id}   (admin / hr)
       Full detail for a single activity log including GPS path.
    ═══════════════════════════════════════════════════════════════ */
    public function getActivityLog(Request $request, $id)
    {
        $log = DB::table('employee_activity_logs as al')
            ->join('users as u', 'u.id', '=', 'al.user_id')
            ->leftJoin('employee_profiles as ep', 'ep.id', '=', 'al.employee_profile_id')
            ->leftJoin('departments as d', 'd.id', '=', 'ep.department_id')
            ->leftJoin('employee_attendance as ea', 'ea.id', '=', 'al.attendance_id')
            ->where('al.id', $id)
            ->whereNull('al.deleted_at')
            ->select([
                'al.*',
                'u.name as employee_name', 'u.email as employee_email',
                'ep.employee_code', 'ep.designation',
                'd.name as department_name',
                'ea.status as attendance_status',
                'ea.check_in_time', 'ea.check_out_time',
                'ea.total_working_minutes', 'ea.work_mode',
            ])
            ->first();

        if (!$log) {
            return response()->json(['status' => 'error', 'message' => 'Activity log not found.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $this->formatLog($log),
        ]);
    }

    /* ─── private helpers ─────────────────────────────────────── */

    private function decodeJsonField(mixed $value): mixed
    {
        if ($value === null || $value === '') return null;
        if (is_array($value) || is_object($value)) return $value;
        return json_decode($value, true);
    }

    private function formatLog(object $log): array
    {
        $data = (array) $log;
        foreach (['gps_path', 'wifi_networks_seen', 'raw_events', 'metadata'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true) ?? [];
            }
        }
        return $data;
    }
}
