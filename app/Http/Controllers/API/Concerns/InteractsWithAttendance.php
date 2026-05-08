<?php

namespace App\Http\Controllers\API\Concerns;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait InteractsWithAttendance
{
    protected function attendanceActor(Request $request): array
    {
        return [
            'id' => (int) ($request->attributes->get('auth_user_id') ?? $request->attributes->get('auth_tokenable_id') ?? 0),
            'uuid' => (string) ($request->attributes->get('auth_user_uuid') ?? ''),
            'name' => (string) ($request->attributes->get('auth_name') ?? ''),
            'role' => strtolower(trim((string) ($request->attributes->get('auth_role') ?? ''))),
            'ip' => $request->ip(),
        ];
    }

    protected function decodeJson($value, array $fallback = []): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return $fallback;
        }

        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : $fallback;
    }

    protected function companySettings(): ?object
    {
        return DB::table('company_settings')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->first();
    }

    protected function employeeContextByUserId(int $userId): ?object
    {
        return DB::table('employee_profiles as ep')
            ->join('users as u', 'u.id', '=', 'ep.user_id')
            ->leftJoin('departments as d', 'd.id', '=', 'ep.department_id')
            ->leftJoin('designations as dg', 'dg.id', '=', 'ep.designation_id')
            ->leftJoin('branches as b', 'b.id', '=', 'ep.branch_id')
            ->leftJoin('shifts as s', 's.id', '=', 'ep.shift_id')
            ->leftJoin('attendance_policies as p', 'p.id', '=', 'ep.attendance_policy_id')
            ->select([
                'u.id as user_id',
                'u.uuid as user_uuid',
                'u.name as user_name',
                'u.email',
                'u.phone_number',
                'u.role',
                'u.status as user_status',
                'u.department_id as user_department_id',
                'u.designation_id as user_designation_id',
                'u.branch_id as user_branch_id',
                'ep.id as employee_profile_id',
                'ep.uuid as employee_profile_uuid',
                'ep.employee_code',
                'ep.department_id',
                'ep.designation_id',
                'ep.branch_id',
                'ep.shift_id',
                'ep.attendance_policy_id',
                'ep.manager_user_id',
                'ep.employment_type',
                'ep.work_mode',
                'ep.join_date',
                'ep.confirmation_date',
                'ep.exit_date',
                'ep.offline_attendance_enabled',
                'ep.field_attendance_enabled',
                'ep.wfh_attendance_enabled',
                'ep.continuous_tracking_enabled',
                'ep.face_image_path',
                'ep.notes',
                'ep.metadata as employee_metadata',
                'ep.status as employee_status',
                'd.name as department_name',
                'dg.name as designation_name',
                'b.name as branch_name',
                'b.address as branch_address',
                'b.latitude as branch_latitude',
                'b.longitude as branch_longitude',
                'b.geofence_radius_meters',
                'b.wifi_only as branch_wifi_only',
                'b.allow_mobile_data as branch_allow_mobile_data',
                'b.allow_outside_geofence as branch_allow_outside_geofence',
                'b.metadata as branch_metadata',
                's.name as shift_name',
                's.start_time',
                's.end_time',
                's.allow_cross_day',
                's.grace_minutes',
                's.late_after_time',
                's.half_day_working_minutes',
                's.full_day_working_minutes',
                's.minimum_working_minutes',
                's.overtime_after_minutes',
                's.early_checkout_before_minutes',
                's.break_minutes',
                's.week_days',
                's.metadata as shift_metadata',
                'p.name as policy_name',
                'p.gps_required',
                'p.selfie_required',
                'p.checkout_selfie_required',
                'p.face_verification_mode',
                'p.liveness_required',
                'p.offline_attendance_allowed',
                'p.offline_sync_limit_hours',
                'p.offline_sync_limit_minutes',
                'p.multiple_punch_allowed',
                'p.geofence_required',
                'p.outside_location_allowed',
                'p.outside_location_requires_approval',
                'p.device_binding_required',
                'p.wifi_ip_restriction_required',
                'p.allow_mobile_data as policy_allow_mobile_data',
                'p.qr_attendance_enabled',
                'p.mark_gps_missing_as_exception',
                'p.auto_approve_clean_records',
                'p.time_drift_tolerance_minutes',
                'p.max_offline_records_per_day',
                'p.continuous_tracking_interval_seconds',
                'p.allow_field_attendance',
                'p.allow_wfh_attendance',
                'p.require_work_note_for_wfh',
                'p.allowed_punch_types',
                'p.metadata as policy_metadata',
            ])
            ->where('u.id', $userId)
            ->whereNull('u.deleted_at')
            ->whereNull('ep.deleted_at')
            ->first();
    }

    protected function storePublicMedia($uploadedFile, string $directory, string $prefix): string|false
    {
        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return false;
        }

        $destDir = public_path(trim($directory, '/'));
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $ext = strtolower($uploadedFile->getClientOriginalExtension() ?: 'bin');
        $filename = $prefix . '_' . now()->format('Ymd_His') . '_' . Str::lower(Str::random(16)) . '.' . $ext;
        $uploadedFile->move($destDir, $filename);

        return '/' . trim($directory, '/') . '/' . $filename;
    }

    protected function createAttendanceAudit(
        Request $request,
        string $module,
        string $action,
        string $targetType,
        ?int $targetId = null,
        ?string $reason = null,
        array $oldValues = [],
        array $newValues = [],
        array $context = []
    ): void {
        try {
            if (!DB::getSchemaBuilder()->hasTable('audit_logs')) {
                return;
            }

            $actor = $this->attendanceActor($request);

            DB::table('audit_logs')->insert([
                'uuid' => (string) Str::uuid(),
                'user_id' => $actor['id'] > 0 ? $actor['id'] : null,
                'module' => $module,
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'request_ip' => $actor['ip'],
                'reason' => $reason,
                'old_values' => !empty($oldValues) ? json_encode($oldValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'new_values' => !empty($newValues) ? json_encode($newValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'context' => !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Audit logging must never block primary attendance flows.
        }
    }

    protected function createAttendanceApproval(array $payload): ?int
    {
        try {
            return DB::table('attendance_approvals')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'user_id' => $payload['user_id'],
                'employee_attendance_id' => $payload['employee_attendance_id'] ?? null,
                'attendance_log_id' => $payload['attendance_log_id'] ?? null,
                'requested_by' => $payload['requested_by'] ?? null,
                'approver_id' => $payload['approver_id'] ?? null,
                'approval_type' => $payload['approval_type'] ?? 'general_exception',
                'status' => $payload['status'] ?? 'pending_approval',
                'requested_at' => $payload['requested_at'] ?? now(),
                'decided_at' => $payload['decided_at'] ?? null,
                'reason' => $payload['reason'] ?? null,
                'approver_remarks' => $payload['approver_remarks'] ?? null,
                'old_values' => !empty($payload['old_values']) ? json_encode($payload['old_values'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'new_values' => !empty($payload['new_values']) ? json_encode($payload['new_values'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'snapshot' => !empty($payload['snapshot']) ? json_encode($payload['snapshot'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function parseClientTimestamp(?string $value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function shiftDateWindow(string $attendanceDate, ?string $startTime, ?string $endTime, $allowCrossDay): array
    {
        $date = Carbon::parse($attendanceDate)->startOfDay();
        $start = $startTime ? Carbon::parse($attendanceDate . ' ' . $startTime) : null;
        $end = $endTime ? Carbon::parse($attendanceDate . ' ' . $endTime) : null;
        $crossDay = filter_var($allowCrossDay, FILTER_VALIDATE_BOOLEAN);

        if ($start && $end && ($crossDay || $end->lessThanOrEqualTo($start))) {
            $end->addDay();
        }

        return [$date, $start, $end];
    }

    protected function recalculateAttendanceRecord(int $recordId, object $employee, ?string $forcedApprovalStatus = null): ?object
    {
        $record = DB::table('employee_attendance')->where('id', $recordId)->first();
        if (!$record) {
            return null;
        }

        [$date, $shiftStart, $shiftEnd] = $this->shiftDateWindow(
            (string) $record->attendance_date,
            $employee->start_time ? substr((string) $employee->start_time, 0, 5) : null,
            $employee->end_time ? substr((string) $employee->end_time, 0, 5) : null,
            $employee->allow_cross_day
        );

        $checkIn = !empty($record->check_in_time) ? Carbon::parse($record->check_in_time) : null;
        $checkOut = !empty($record->check_out_time) ? Carbon::parse($record->check_out_time) : null;
        $approvalStatus = $forcedApprovalStatus ?? ($record->approval_status ?: 'approved');

        $lateMinutes = null;
        if ($checkIn && $shiftStart) {
            $lateBase = !empty($employee->late_after_time)
                ? Carbon::parse($record->attendance_date . ' ' . substr((string) $employee->late_after_time, 0, 5))
                : $shiftStart->copy()->addMinutes((int) ($employee->grace_minutes ?? 0));

            if ($checkIn->greaterThan($lateBase)) {
                $lateMinutes = $lateBase->diffInMinutes($checkIn);
            }
        }

        $workedMinutes = null;
        $earlyCheckoutMinutes = null;
        $overtimeMinutes = null;

        if ($checkIn && $checkOut && $checkOut->greaterThanOrEqualTo($checkIn)) {
            $workedMinutes = max(0, $checkIn->diffInMinutes($checkOut) - (int) ($employee->break_minutes ?? 0));

            if ($shiftEnd && $checkOut->lessThan($shiftEnd)) {
                $earlyCheckoutMinutes = $checkOut->diffInMinutes($shiftEnd);
            }

            $overtimeThreshold = (int) ($employee->overtime_after_minutes ?? 0);
            if ($overtimeThreshold > 0 && $workedMinutes > $overtimeThreshold) {
                $overtimeMinutes = $workedMinutes - $overtimeThreshold;
            } elseif (!$overtimeThreshold && !empty($employee->full_day_working_minutes) && $workedMinutes > (int) $employee->full_day_working_minutes) {
                $overtimeMinutes = $workedMinutes - (int) $employee->full_day_working_minutes;
            }
        }

        $status = $approvalStatus === 'pending_approval' ? 'pending_approval' : 'present';

        if ($approvalStatus === 'rejected') {
            $status = 'rejected';
        } elseif ($approvalStatus !== 'pending_approval') {
            if ($workedMinutes !== null) {
                $halfDayThreshold = (int) ($employee->half_day_working_minutes ?? 0);
                $fullDayThreshold = (int) ($employee->full_day_working_minutes ?? 0);
                $minimumThreshold = (int) ($employee->minimum_working_minutes ?? 0);
                $halfThreshold = $halfDayThreshold ?: ($minimumThreshold ?: $fullDayThreshold);

                if ($halfThreshold > 0 && $workedMinutes < $halfThreshold) {
                    $status = 'half_day';
                } elseif (!empty($lateMinutes)) {
                    $status = 'late';
                } else {
                    $status = 'present';
                }
            } elseif (!empty($lateMinutes)) {
                $status = 'late';
            }
        }

        DB::table('employee_attendance')->where('id', $recordId)->update([
            'late_minutes' => $lateMinutes,
            'total_working_minutes' => $workedMinutes,
            'early_checkout_minutes' => $earlyCheckoutMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'status' => $status,
            'approval_status' => $approvalStatus,
            'updated_at' => now(),
        ]);

        return DB::table('employee_attendance')->where('id', $recordId)->first();
    }

    protected function distanceInMeters(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): ?float
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
            return null;
        }

        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    protected function evaluateGeofence(?object $employee, ?float $latitude, ?float $longitude, string $workMode): array
    {
        $requiresGeofence = $workMode === 'office' && filter_var($employee?->geofence_required ?? false, FILTER_VALIDATE_BOOLEAN);
        $branchLat = isset($employee?->branch_latitude) ? (float) $employee->branch_latitude : null;
        $branchLng = isset($employee?->branch_longitude) ? (float) $employee->branch_longitude : null;
        $radius = isset($employee?->geofence_radius_meters) ? (int) $employee->geofence_radius_meters : null;

        if ($branchLat === null || $branchLng === null || !$radius) {
            return [
                'required' => $requiresGeofence,
                'inside' => null,
                'distance_meters' => null,
                'reason' => $requiresGeofence ? 'geofence_not_configured' : null,
            ];
        }

        $distance = $this->distanceInMeters($branchLat, $branchLng, $latitude, $longitude);
        $inside = $distance !== null ? $distance <= $radius : null;

        return [
            'required' => $requiresGeofence,
            'inside' => $inside,
            'distance_meters' => $distance,
            'reason' => $inside === false ? 'outside_geofence' : null,
        ];
    }

    protected function ipMatchesPattern(?string $ip, string $pattern): bool
    {
        if (!$ip || !$pattern) {
            return false;
        }

        if (!str_contains($pattern, '/')) {
            return trim($ip) === trim($pattern);
        }

        [$subnet, $prefix] = explode('/', $pattern, 2);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $prefix = (int) $prefix;
        $mask = -1 << (32 - $prefix);
        $subnetLong &= $mask;

        return ($ipLong & $mask) === $subnetLong;
    }

    protected function evaluateNetwork(Request $request, ?int $branchId, ?object $employee, string $networkType, string $workMode): array
    {
        $requestIp = $request->ip();
        $requiresNetwork = $workMode === 'office'
            && (
                filter_var($employee?->wifi_ip_restriction_required ?? false, FILTER_VALIDATE_BOOLEAN)
                || filter_var($employee?->branch_wifi_only ?? false, FILTER_VALIDATE_BOOLEAN)
            );

        $patterns = [];
        if ($branchId) {
            $patterns = DB::table('branch_allowed_networks')
                ->where('branch_id', $branchId)
                ->where('is_active', 1)
                ->whereNull('deleted_at')
                ->pluck('ip_pattern')
                ->filter()
                ->values()
                ->all();
        }

        $matchedPattern = null;
        foreach ($patterns as $pattern) {
            if ($this->ipMatchesPattern($requestIp, (string) $pattern)) {
                $matchedPattern = (string) $pattern;
                break;
            }
        }

        return [
            'required' => $requiresNetwork,
            'request_ip' => $requestIp,
            'matched' => $matchedPattern !== null,
            'matched_pattern' => $matchedPattern,
            'mobile_data_blocked' => false,
            'patterns' => $patterns,
            'reason' => $matchedPattern === null && $requiresNetwork ? 'wifi_ip_not_allowed' : null,
        ];
    }

    protected function ensureDeviceRegistration(int $userId, ?int $employeeProfileId, ?string $deviceId, array $deviceMeta, bool $required): array
    {
        if (!$deviceId) {
            return [
                'valid' => !$required,
                'device_id' => $deviceId,
                'auto_bound' => false,
                'reason' => $required ? 'device_id_missing' : null,
            ];
        }

        $existing = DB::table('device_registrations')
            ->where('user_id', $userId)
            ->where('device_id', $deviceId)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            DB::table('device_registrations')->where('id', $existing->id)->update([
                'last_ip' => $deviceMeta['last_ip'] ?? null,
                'last_seen_at' => now(),
                'app_version' => $deviceMeta['app_version'] ?? ($existing->app_version ?? null),
                'updated_at' => now(),
            ]);

            return [
                'valid' => true,
                'device_id' => $deviceId,
                'auto_bound' => false,
                'reason' => null,
            ];
        }

        $activeForUser = DB::table('device_registrations')
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->exists();

        if ($required && $activeForUser) {
            return [
                'valid' => false,
                'device_id' => $deviceId,
                'auto_bound' => false,
                'reason' => 'device_not_bound',
            ];
        }

        DB::table('device_registrations')->insert([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'employee_profile_id' => $employeeProfileId,
            'device_id' => $deviceId,
            'device_name' => $deviceMeta['device_name'] ?? null,
            'device_platform' => $deviceMeta['device_platform'] ?? null,
            'device_model' => $deviceMeta['device_model'] ?? null,
            'os_version' => $deviceMeta['os_version'] ?? null,
            'app_version' => $deviceMeta['app_version'] ?? null,
            'last_ip' => $deviceMeta['last_ip'] ?? null,
            'bound_at' => now(),
            'last_seen_at' => now(),
            'is_active' => 1,
            'metadata' => !empty($deviceMeta['metadata']) ? json_encode($deviceMeta['metadata'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'valid' => true,
            'device_id' => $deviceId,
            'auto_bound' => true,
            'reason' => null,
        ];
    }
}
