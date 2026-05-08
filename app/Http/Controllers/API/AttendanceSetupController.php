<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithAttendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AttendanceSetupController extends Controller
{
    use InteractsWithAttendance;

    private const RESOURCES = [
        'departments' => [
            'table' => 'departments',
            'rules' => [
                'name' => 'required|string|max:150',
                'code' => 'sometimes|nullable|string|max:64',
                'description' => 'sometimes|nullable|string',
                'metadata' => 'sometimes|nullable|array',
                'status' => 'sometimes|nullable|in:active,inactive',
            ],
            'search' => ['name', 'code', 'description'],
        ],
        'designations' => [
            'table' => 'designations',
            'rules' => [
                'name' => 'required|string|max:150',
                'code' => 'sometimes|nullable|string|max:64',
                'description' => 'sometimes|nullable|string',
                'metadata' => 'sometimes|nullable|array',
                'status' => 'sometimes|nullable|in:active,inactive',
            ],
            'search' => ['name', 'code', 'description'],
        ],
        'branches' => [
            'table' => 'branches',
            'rules' => [
                'name' => 'required|string|max:150',
                'code' => 'sometimes|nullable|string|max:64',
                'address' => 'sometimes|nullable|string',
                'city' => 'sometimes|nullable|string|max:120',
                'state' => 'sometimes|nullable|string|max:120',
                'country' => 'sometimes|nullable|string|max:120',
                'postal_code' => 'sometimes|nullable|string|max:32',
                'latitude' => 'sometimes|nullable|numeric|between:-90,90',
                'longitude' => 'sometimes|nullable|numeric|between:-180,180',
                'geofence_radius_meters' => 'sometimes|nullable|integer|min:1|max:100000',
                'wifi_only' => 'sometimes|nullable|boolean',
                'allow_mobile_data' => 'sometimes|nullable|boolean',
                'allow_outside_geofence' => 'sometimes|nullable|boolean',
                'metadata' => 'sometimes|nullable|array',
                'status' => 'sometimes|nullable|in:active,inactive',
            ],
            'search' => ['name', 'code', 'address', 'city', 'state'],
        ],
        'shifts' => [
            'table' => 'shifts',
            'rules' => [
                'name' => 'required|string|max:150',
                'code' => 'sometimes|nullable|string|max:64',
                'start_time' => 'sometimes|nullable|date_format:H:i',
                'end_time' => 'sometimes|nullable|date_format:H:i',
                'allow_cross_day' => 'sometimes|nullable|boolean',
                'grace_minutes' => 'sometimes|nullable|integer|min:0|max:1440',
                'late_after_time' => 'sometimes|nullable|date_format:H:i',
                'half_day_working_minutes' => 'sometimes|nullable|integer|min:0|max:1440',
                'full_day_working_minutes' => 'sometimes|nullable|integer|min:0|max:1440',
                'minimum_working_minutes' => 'sometimes|nullable|integer|min:0|max:1440',
                'overtime_after_minutes' => 'sometimes|nullable|integer|min:0|max:2880',
                'early_checkout_before_minutes' => 'sometimes|nullable|integer|min:0|max:1440',
                'break_minutes' => 'sometimes|nullable|integer|min:0|max:1440',
                'week_days' => 'sometimes|nullable|array',
                'metadata' => 'sometimes|nullable|array',
                'status' => 'sometimes|nullable|in:active,inactive',
            ],
            'search' => ['name', 'code'],
        ],
        'attendance-policies' => [
            'table' => 'attendance_policies',
            'rules' => [
                'name' => 'required|string|max:150',
                'code' => 'sometimes|nullable|string|max:64',
                'description' => 'sometimes|nullable|string',
                'gps_required' => 'sometimes|nullable|boolean',
                'selfie_required' => 'sometimes|nullable|boolean',
                'checkout_selfie_required' => 'sometimes|nullable|boolean',
                'face_verification_mode' => 'sometimes|nullable|string|max:40',
                'liveness_required' => 'sometimes|nullable|boolean',
                'offline_attendance_allowed' => 'sometimes|nullable|boolean',
                'offline_sync_limit_hours' => 'sometimes|nullable|integer|min:0|max:240',
                'offline_sync_limit_minutes' => 'sometimes|nullable|integer|min:0|max:20000',
                'multiple_punch_allowed' => 'sometimes|nullable|boolean',
                'geofence_required' => 'sometimes|nullable|boolean',
                'outside_location_allowed' => 'sometimes|nullable|boolean',
                'outside_location_requires_approval' => 'sometimes|nullable|boolean',
                'device_binding_required' => 'sometimes|nullable|boolean',
                'wifi_ip_restriction_required' => 'sometimes|nullable|boolean',
                'allow_mobile_data' => 'sometimes|nullable|boolean',
                'qr_attendance_enabled' => 'sometimes|nullable|boolean',
                'mark_gps_missing_as_exception' => 'sometimes|nullable|boolean',
                'auto_approve_clean_records' => 'sometimes|nullable|boolean',
                'time_drift_tolerance_minutes' => 'sometimes|nullable|integer|min:0|max:1440',
                'max_offline_records_per_day' => 'sometimes|nullable|integer|min:0|max:1000',
                'continuous_tracking_interval_seconds' => 'sometimes|nullable|integer|min:0|max:86400',
                'allow_field_attendance' => 'sometimes|nullable|boolean',
                'allow_wfh_attendance' => 'sometimes|nullable|boolean',
                'require_work_note_for_wfh' => 'sometimes|nullable|boolean',
                'allowed_punch_types' => 'sometimes|nullable|array',
                'metadata' => 'sometimes|nullable|array',
                'status' => 'sometimes|nullable|in:active,inactive',
            ],
            'search' => ['name', 'code', 'description'],
        ],
        'holidays' => [
            'table' => 'holidays',
            'rules' => [
                'name' => 'required|string|max:150',
                'holiday_date' => 'sometimes|nullable|date',
                'branch_id' => 'sometimes|nullable|integer|exists:branches,id',
                'department_id' => 'sometimes|nullable|integer|exists:departments,id',
                'holiday_type' => 'sometimes|nullable|string|max:40',
                'description' => 'sometimes|nullable|string',
                'metadata' => 'sometimes|nullable|array',
                'status' => 'sometimes|nullable|in:active,inactive',
            ],
            'search' => ['name', 'holiday_type', 'description'],
        ],
        'leave-types' => [
            'table' => 'leave_types',
            'rules' => [
                'name' => 'required|string|max:150',
                'code' => 'sometimes|nullable|string|max:64',
                'days_allowed' => 'sometimes|nullable|numeric|min:0|max:365',
                'is_paid' => 'sometimes|nullable|boolean',
                'requires_approval' => 'sometimes|nullable|boolean',
                'requires_document' => 'sometimes|nullable|boolean',
                'description' => 'sometimes|nullable|string',
                'metadata' => 'sometimes|nullable|array',
                'status' => 'sometimes|nullable|in:active,inactive',
            ],
            'search' => ['name', 'code', 'description'],
        ],
    ];

    public function showCompany()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->companySettings(),
        ]);
    }

    public function requestIp(Request $request)
    {
        $ip = $request->ip();
        return response()->json([
            'status' => 'success',
            'data' => [
                'ip' => $ip,
                'is_loopback' => in_array($ip, ['127.0.0.1', '::1'], true),
                'hint' => in_array($ip, ['127.0.0.1', '::1'], true)
                    ? 'Localhost detected. Add 127.0.0.1 or ::1 to allowed IPs for local testing.'
                    : null,
            ],
        ]);
    }

    public function updateCompany(Request $request)
    {
        $rules = [
            'company_name' => 'sometimes|nullable|string|max:150',
            'legal_name' => 'sometimes|nullable|string|max:150',
            'company_code' => 'sometimes|nullable|string|max:64',
            'timezone' => 'sometimes|nullable|string|max:64',
            'working_days' => 'sometimes|nullable|array',
            'weekly_offs' => 'sometimes|nullable|array',
            'attendance_mode' => 'sometimes|nullable|string|max:40',
            'default_grace_time_minutes' => 'sometimes|nullable|integer|min:0|max:1440',
            'offline_sync_limit_hours' => 'sometimes|nullable|integer|min:0|max:240',
            'default_currency' => 'sometimes|nullable|string|max:12',
            'date_format' => 'sometimes|nullable|string|max:32',
            'time_format' => 'sometimes|nullable|string|max:32',
            'metadata' => 'sometimes|nullable|array',
            'status' => 'sometimes|nullable|in:active,inactive',
        ];

        $validated = $request->validate($rules);
        $company = $this->companySettings();
        $payload = $this->normalizePayload($validated);

        if (!$company) {
            $payload['uuid'] = (string) Str::uuid();
            $payload['created_at'] = now();
            $payload['updated_at'] = now();
            $id = DB::table('company_settings')->insertGetId($payload);
            $record = DB::table('company_settings')->where('id', $id)->first();
        } else {
            DB::table('company_settings')->where('id', $company->id)->update($payload + ['updated_at' => now()]);
            $record = DB::table('company_settings')->where('id', $company->id)->first();
        }

        $this->createAttendanceAudit(
            $request,
            'company_settings',
            'update',
            'company_settings',
            $record?->id,
            'Updated company attendance configuration.',
            (array) ($company ?? []),
            (array) ($record ?? [])
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Company settings saved successfully.',
            'data' => $record,
        ]);
    }

    public function index(Request $request)
    {
        $resource = (string) $request->route('resource');
        $config = $this->resourceConfig($resource);
        $table = $config['table'];
        $perPage = max(1, min(200, (int) $request->query('per_page', 20)));

        $query = DB::table($table)->whereNull('deleted_at');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('q')) {
            $term = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($config, $term) {
                foreach ($config['search'] as $index => $column) {
                    $index === 0
                        ? $builder->where($column, 'like', $term)
                        : $builder->orWhere($column, 'like', $term);
                }
            });
        }

        $paginator = $query->orderByDesc('id')->paginate($perPage);

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

    public function store(Request $request)
    {
        $resource = (string) $request->route('resource');
        $config = $this->resourceConfig($resource);
        $validated = $this->validateForResource($request, $config['rules'], false);
        $payload = $this->normalizePayload($validated);
        $payload['uuid'] = (string) Str::uuid();
        $payload['created_at'] = now();
        $payload['updated_at'] = now();

        $id = DB::table($config['table'])->insertGetId($payload);
        $record = DB::table($config['table'])->where('id', $id)->first();

        $this->createAttendanceAudit(
            $request,
            $config['table'],
            'create',
            $config['table'],
            $id,
            'Created attendance setup record.',
            [],
            (array) $record
        );

        return response()->json([
            'status' => 'success',
            'message' => Str::headline(str_replace('-', ' ', $resource)) . ' created successfully.',
            'data' => $record,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $resource = (string) $request->route('resource');
        $config = $this->resourceConfig($resource);
        $record = $this->resolveSetupRecord($config['table'], $id);

        if (!$record) {
            return response()->json([
                'status' => 'error',
                'message' => $this->isValidSetupIdentifier($id) ? 'Record not found' : 'Invalid record identifier',
            ], $this->isValidSetupIdentifier($id) ? 404 : 400);
        }

        return response()->json(['status' => 'success', 'data' => $record]);
    }

    public function update(Request $request, $id)
    {
        $resource = (string) $request->route('resource');
        $config = $this->resourceConfig($resource);
        $existing = $this->resolveSetupRecord($config['table'], $id);

        if (!$existing) {
            return response()->json([
                'status' => 'error',
                'message' => $this->isValidSetupIdentifier($id) ? 'Record not found' : 'Invalid record identifier',
            ], $this->isValidSetupIdentifier($id) ? 404 : 400);
        }

        $validated = $this->validateForResource($request, $config['rules'], true);
        $payload = $this->normalizePayload($validated);

        if (empty($payload)) {
            return response()->json(['status' => 'error', 'message' => 'Nothing to update'], 400);
        }

        DB::table($config['table'])->where('id', $existing->id)->update($payload + ['updated_at' => now()]);
        $record = DB::table($config['table'])->where('id', $existing->id)->first();

        $this->createAttendanceAudit(
            $request,
            $config['table'],
            'update',
            $config['table'],
            $existing->id,
            'Updated attendance setup record.',
            (array) $existing,
            (array) $record
        );

        return response()->json([
            'status' => 'success',
            'message' => Str::headline(str_replace('-', ' ', $resource)) . ' updated successfully.',
            'data' => $record,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $resource = (string) $request->route('resource');
        $config = $this->resourceConfig($resource);
        $existing = $this->resolveSetupRecord($config['table'], $id);

        if (!$existing) {
            return response()->json([
                'status' => 'error',
                'message' => $this->isValidSetupIdentifier($id) ? 'Record not found' : 'Invalid record identifier',
            ], $this->isValidSetupIdentifier($id) ? 404 : 400);
        }

        DB::table($config['table'])->where('id', $existing->id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        $this->createAttendanceAudit(
            $request,
            $config['table'],
            'delete',
            $config['table'],
            $existing->id,
            'Soft deleted attendance setup record.',
            (array) $existing,
            []
        );

        return response()->json([
            'status' => 'success',
            'message' => Str::headline(str_replace('-', ' ', $resource)) . ' deleted successfully.',
        ]);
    }

    public function indexBranchNetworks(int $branchId)
    {
        $branch = DB::table('branches')->where('id', $branchId)->whereNull('deleted_at')->first();
        if (!$branch) {
            return response()->json(['status' => 'error', 'message' => 'Branch not found'], 404);
        }

        $rows = DB::table('branch_allowed_networks')
            ->where('branch_id', $branchId)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get();

        return response()->json(['status' => 'success', 'data' => $rows]);
    }

    public function storeBranchNetwork(Request $request, int $branchId)
    {
        $branch = DB::table('branches')->where('id', $branchId)->whereNull('deleted_at')->first();
        if (!$branch) {
            return response()->json(['status' => 'error', 'message' => 'Branch not found'], 404);
        }

        $validated = $request->validate([
            'label' => 'sometimes|nullable|string|max:150',
            'ip_pattern' => 'required|string|max:120',
            'network_type' => 'sometimes|nullable|string|max:32',
            'is_active' => 'sometimes|nullable|boolean',
            'notes' => 'sometimes|nullable|string',
            'metadata' => 'sometimes|nullable|array',
        ]);

        $payload = $this->normalizePayload($validated);
        $payload['uuid'] = (string) Str::uuid();
        $payload['branch_id'] = $branchId;
        $payload['created_at'] = now();
        $payload['updated_at'] = now();

        $id = DB::table('branch_allowed_networks')->insertGetId($payload);
        $record = DB::table('branch_allowed_networks')->where('id', $id)->first();

        $this->createAttendanceAudit(
            $request,
            'branch_allowed_networks',
            'create',
            'branch_allowed_networks',
            $id,
            'Added branch allowed Wi-Fi/IP restriction.',
            [],
            (array) $record
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Allowed network added successfully.',
            'data' => $record,
        ], 201);
    }

    public function updateBranchNetwork(Request $request, int $branchId, int $networkId)
    {
        $network = DB::table('branch_allowed_networks')
            ->where('id', $networkId)
            ->where('branch_id', $branchId)
            ->whereNull('deleted_at')
            ->first();

        if (!$network) {
            return response()->json(['status' => 'error', 'message' => 'Allowed network not found'], 404);
        }

        $validated = $request->validate([
            'label' => 'sometimes|nullable|string|max:150',
            'ip_pattern' => 'sometimes|nullable|string|max:120',
            'network_type' => 'sometimes|nullable|string|max:32',
            'is_active' => 'sometimes|nullable|boolean',
            'notes' => 'sometimes|nullable|string',
            'metadata' => 'sometimes|nullable|array',
        ]);

        $payload = $this->normalizePayload($validated);
        if (empty($payload)) {
            return response()->json(['status' => 'error', 'message' => 'Nothing to update'], 400);
        }

        DB::table('branch_allowed_networks')->where('id', $networkId)->update($payload + ['updated_at' => now()]);
        $record = DB::table('branch_allowed_networks')->where('id', $networkId)->first();

        $this->createAttendanceAudit(
            $request,
            'branch_allowed_networks',
            'update',
            'branch_allowed_networks',
            $networkId,
            'Updated allowed Wi-Fi/IP restriction.',
            (array) $network,
            (array) $record
        );

        return response()->json(['status' => 'success', 'message' => 'Allowed network updated successfully.', 'data' => $record]);
    }

    public function destroyBranchNetwork(Request $request, int $branchId, int $networkId)
    {
        $network = DB::table('branch_allowed_networks')
            ->where('id', $networkId)
            ->where('branch_id', $branchId)
            ->whereNull('deleted_at')
            ->first();

        if (!$network) {
            return response()->json(['status' => 'error', 'message' => 'Allowed network not found'], 404);
        }

        DB::table('branch_allowed_networks')->where('id', $networkId)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        $this->createAttendanceAudit(
            $request,
            'branch_allowed_networks',
            'delete',
            'branch_allowed_networks',
            $networkId,
            'Removed allowed Wi-Fi/IP restriction.',
            (array) $network,
            []
        );

        return response()->json(['status' => 'success', 'message' => 'Allowed network deleted successfully.']);
    }

    private function resourceConfig(string $resource): array
    {
        abort_unless(isset(self::RESOURCES[$resource]), 404, 'Unknown attendance resource');
        return self::RESOURCES[$resource];
    }

    private function validateForResource(Request $request, array $rules, bool $partial): array
    {
        if (!$partial) {
            return $request->validate($rules);
        }

        $partialRules = [];
        foreach ($rules as $field => $rule) {
            $partialRules[$field] = is_array($rule)
                ? array_values(array_unique(array_merge(['sometimes'], $rule)))
                : 'sometimes|' . $rule;
        }

        return $request->validate($partialRules);
    }

    private function normalizePayload(array $validated): array
    {
        $payload = $validated;

        foreach (['metadata', 'working_days', 'weekly_offs', 'week_days', 'allowed_punch_types'] as $jsonField) {
            if (array_key_exists($jsonField, $payload)) {
                $payload[$jsonField] = $payload[$jsonField] !== null
                    ? json_encode($payload[$jsonField], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null;
            }
        }

        return $payload;
    }

    private function isValidSetupIdentifier($identifier): bool
    {
        $value = trim(urldecode((string) $identifier));
        return $value !== '';
    }

    private function resolveSetupRecord(string $table, $identifier): ?object
    {
        $value = trim(urldecode((string) $identifier));
        if ($value === '') {
            return null;
        }

        $query = DB::table($table)->whereNull('deleted_at');

        if (ctype_digit($value)) {
            return $query->where('id', (int) $value)->first();
        }

        if (Schema::hasColumn($table, 'uuid')) {
            $record = (clone $query)->where('uuid', $value)->first();
            if ($record) {
                return $record;
            }

            $record = (clone $query)
                ->whereRaw('LOWER(uuid) = ?', [Str::lower($value)])
                ->first();
            if ($record) {
                return $record;
            }
        }

        if (Schema::hasColumn($table, 'code')) {
            $record = (clone $query)->where('code', $value)->first();
            if ($record) {
                return $record;
            }

            $record = (clone $query)
                ->whereRaw('LOWER(code) = ?', [Str::lower($value)])
                ->first();
            if ($record) {
                return $record;
            }
        }

        if (Schema::hasColumn($table, 'name')) {
            $record = (clone $query)->where('name', $value)->first();
            if ($record) {
                return $record;
            }

            $record = (clone $query)
                ->whereRaw('LOWER(name) = ?', [Str::lower($value)])
                ->first();
            if ($record) {
                return $record;
            }
        }

        return null;
    }
}
