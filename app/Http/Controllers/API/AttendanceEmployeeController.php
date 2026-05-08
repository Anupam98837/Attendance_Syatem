<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithAttendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AttendanceEmployeeController extends Controller
{
    use InteractsWithAttendance;

    public function index(Request $request)
    {
        $perPage = max(1, min(200, (int) $request->query('per_page', 20)));

        $query = DB::table('employee_profiles as ep')
            ->join('users as u', 'u.id', '=', 'ep.user_id')
            ->leftJoin('departments as d', 'd.id', '=', 'ep.department_id')
            ->leftJoin('designations as dg', 'dg.id', '=', 'ep.designation_id')
            ->leftJoin('branches as b', 'b.id', '=', 'ep.branch_id')
            ->leftJoin('shifts as s', 's.id', '=', 'ep.shift_id')
            ->leftJoin('attendance_policies as p', 'p.id', '=', 'ep.attendance_policy_id')
            ->whereNull('ep.deleted_at')
            ->whereNull('u.deleted_at')
            ->select([
                'ep.id',
                'ep.uuid',
                'ep.employee_code',
                'ep.employment_type',
                'ep.work_mode',
                'ep.status as employee_status',
                'ep.join_date',
                'u.id as user_id',
                'u.uuid as user_uuid',
                'u.name',
                'u.email',
                'u.phone_number',
                'u.role',
                'u.status as user_status',
                'd.name as department_name',
                'dg.name as designation_name',
                'b.name as branch_name',
                's.name as shift_name',
                'p.name as policy_name',
            ]);

        if ($request->filled('q')) {
            $term = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($term) {
                $builder->where('u.name', 'like', $term)
                    ->orWhere('u.email', 'like', $term)
                    ->orWhere('u.phone_number', 'like', $term)
                    ->orWhere('ep.employee_code', 'like', $term);
            });
        }

        foreach (['department_id', 'designation_id', 'branch_id', 'shift_id', 'attendance_policy_id'] as $field) {
            if ($request->filled($field)) {
                $query->where("ep.$field", (int) $request->query($field));
            }
        }

        if ($request->filled('status')) {
            $query->where('ep.status', $request->query('status'));
        }

        $paginator = $query->orderByDesc('ep.id')->paginate($perPage);

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
        $validated = $this->validateEmployeeRequest($request, false);
        $actor = $this->attendanceActor($request);
        $faceImagePath = $this->resolveFaceImage($request);
        $userImagePath = $this->resolveUserImage($request);

        DB::beginTransaction();

        try {
            if (DB::table('users')->where('email', $validated['email'])->whereNull('deleted_at')->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Email already exists'], 422);
            }

            if (!empty($validated['phone_number']) && DB::table('users')->where('phone_number', $validated['phone_number'])->whereNull('deleted_at')->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Phone number already exists'], 422);
            }

            if (!empty($validated['employee_code']) && DB::table('employee_profiles')->where('employee_code', $validated['employee_code'])->whereNull('deleted_at')->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Employee code already exists'], 422);
            }

            $userId = DB::table('users')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'name' => $validated['name'],
                'slug' => $this->buildUniqueUserSlug($validated['name']),
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'] ?? null,
                'alternative_email' => $validated['alternative_email'] ?? null,
                'alternative_phone_number' => $validated['alternative_phone_number'] ?? null,
                'whatsapp_number' => $validated['whatsapp_number'] ?? null,
                'password' => Hash::make($validated['password']),
                'image' => $userImagePath ?: ($validated['image_path'] ?? null),
                'address' => $validated['address'] ?? null,
                'role' => 'employee',
                'role_short_form' => 'EMP',
                'department_id' => $validated['department_id'] ?? null,
                'designation_id' => $validated['designation_id'] ?? null,
                'branch_id' => $validated['branch_id'] ?? null,
                'status' => $validated['status'] ?? 'active',
                'remember_token' => Str::random(60),
                'created_by' => $actor['id'] ?: null,
                'created_at_ip' => $request->ip(),
                'metadata' => json_encode([
                    'source' => 'attendance_employee_create',
                    'employee_code' => $validated['employee_code'] ?? null,
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $profileId = DB::table('employee_profiles')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'user_id' => $userId,
                'employee_code' => $validated['employee_code'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'designation_id' => $validated['designation_id'] ?? null,
                'branch_id' => $validated['branch_id'] ?? null,
                'shift_id' => $validated['shift_id'] ?? null,
                'attendance_policy_id' => $validated['attendance_policy_id'] ?? null,
                'manager_user_id' => $validated['manager_user_id'] ?? null,
                'employment_type' => $validated['employment_type'] ?? null,
                'work_mode' => $validated['work_mode'] ?? 'office',
                'join_date' => $validated['join_date'] ?? null,
                'confirmation_date' => $validated['confirmation_date'] ?? null,
                'exit_date' => $validated['exit_date'] ?? null,
                'offline_attendance_enabled' => $validated['offline_attendance_enabled'] ?? null,
                'field_attendance_enabled' => $validated['field_attendance_enabled'] ?? null,
                'wfh_attendance_enabled' => $validated['wfh_attendance_enabled'] ?? null,
                'continuous_tracking_enabled' => $validated['continuous_tracking_enabled'] ?? null,
                'face_image_path' => $faceImagePath ?: ($validated['face_image_path'] ?? null),
                'notes' => $validated['notes'] ?? null,
                'metadata' => !empty($validated['metadata']) ? json_encode($validated['metadata'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'status' => $validated['employee_status'] ?? 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($validated['device_id'])) {
                $this->ensureDeviceRegistration($userId, $profileId, $validated['device_id'], [
                    'device_name' => $validated['device_name'] ?? null,
                    'device_platform' => $validated['device_platform'] ?? null,
                    'device_model' => $validated['device_model'] ?? null,
                    'os_version' => $validated['os_version'] ?? null,
                    'app_version' => $validated['app_version'] ?? null,
                    'last_ip' => $request->ip(),
                ], false);
            }

            DB::commit();

            $record = $this->employeeDetails($profileId);
            $this->createAttendanceAudit($request, 'employees', 'create', 'employee_profiles', $profileId, 'Created employee attendance profile.', [], $record);

            return response()->json([
                'status' => 'success',
                'message' => 'Employee created successfully.',
                'data' => $record,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Could not create employee.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id)
    {
        $record = $this->employeeDetails($id);
        if (!$record) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $record,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $profile = DB::table('employee_profiles')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$profile) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found'], 404);
        }

        $user = DB::table('users')->where('id', $profile->user_id)->whereNull('deleted_at')->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Linked user not found'], 404);
        }

        $validated = $this->validateEmployeeRequest($request, true);
        $faceImagePath = $this->resolveFaceImage($request);
        $userImagePath = $this->resolveUserImage($request);

        DB::beginTransaction();

        try {
            if (!empty($validated['email']) && DB::table('users')->where('email', $validated['email'])->where('id', '!=', $user->id)->whereNull('deleted_at')->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Email already exists'], 422);
            }

            if (!empty($validated['phone_number']) && DB::table('users')->where('phone_number', $validated['phone_number'])->where('id', '!=', $user->id)->whereNull('deleted_at')->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Phone number already exists'], 422);
            }

            if (!empty($validated['employee_code']) && DB::table('employee_profiles')->where('employee_code', $validated['employee_code'])->where('id', '!=', $id)->whereNull('deleted_at')->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Employee code already exists'], 422);
            }

            $userUpdates = [];
            foreach ([
                'name', 'email', 'phone_number', 'alternative_email', 'alternative_phone_number',
                'whatsapp_number', 'address', 'department_id', 'designation_id', 'branch_id', 'status'
            ] as $field) {
                if (array_key_exists($field, $validated)) {
                    $userUpdates[$field] = $validated[$field];
                }
            }

            if (!empty($validated['password'])) {
                $userUpdates['password'] = Hash::make($validated['password']);
            }

            if (!empty($validated['name']) && $validated['name'] !== $user->name) {
                $userUpdates['slug'] = $this->buildUniqueUserSlug($validated['name'], $user->id);
            }

            if ($userImagePath || !empty($validated['image_path'])) {
                $userUpdates['image'] = $userImagePath ?: $validated['image_path'];
            }

            if (!empty($userUpdates)) {
                $userUpdates['updated_at'] = now();
                DB::table('users')->where('id', $user->id)->update($userUpdates);
            }

            $profileUpdates = [];
            foreach ([
                'employee_code', 'department_id', 'designation_id', 'branch_id', 'shift_id',
                'attendance_policy_id', 'manager_user_id', 'employment_type', 'work_mode',
                'join_date', 'confirmation_date', 'exit_date', 'offline_attendance_enabled',
                'field_attendance_enabled', 'wfh_attendance_enabled', 'continuous_tracking_enabled',
                'face_image_path', 'notes', 'employee_status'
            ] as $field) {
                if (!array_key_exists($field, $validated)) {
                    continue;
                }

                $targetField = $field === 'employee_status' ? 'status' : $field;
                $profileUpdates[$targetField] = $validated[$field];
            }

            if ($faceImagePath) {
                $profileUpdates['face_image_path'] = $faceImagePath;
            }

            if (array_key_exists('metadata', $validated)) {
                $profileUpdates['metadata'] = $validated['metadata'] !== null
                    ? json_encode($validated['metadata'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null;
            }

            if (!empty($profileUpdates)) {
                $profileUpdates['updated_at'] = now();
                DB::table('employee_profiles')->where('id', $id)->update($profileUpdates);
            }

            DB::commit();

            $record = $this->employeeDetails($id);
            $this->createAttendanceAudit($request, 'employees', 'update', 'employee_profiles', $id, 'Updated employee attendance profile.', [
                'user' => (array) $user,
                'profile' => (array) $profile,
            ], $record);

            return response()->json([
                'status' => 'success',
                'message' => 'Employee updated successfully.',
                'data' => $record,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Could not update employee.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function registerDevice(Request $request, int $id)
    {
        $profile = DB::table('employee_profiles')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$profile) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found'], 404);
        }

        $validated = $request->validate([
            'device_id' => 'required|string|max:120',
            'device_name' => 'sometimes|nullable|string|max:150',
            'device_platform' => 'sometimes|nullable|string|max:40',
            'device_model' => 'sometimes|nullable|string|max:120',
            'os_version' => 'sometimes|nullable|string|max:60',
            'app_version' => 'sometimes|nullable|string|max:60',
            'metadata' => 'sometimes|nullable|array',
        ]);

        $result = $this->ensureDeviceRegistration($profile->user_id, $profile->id, $validated['device_id'], [
            'device_name' => $validated['device_name'] ?? null,
            'device_platform' => $validated['device_platform'] ?? null,
            'device_model' => $validated['device_model'] ?? null,
            'os_version' => $validated['os_version'] ?? null,
            'app_version' => $validated['app_version'] ?? null,
            'last_ip' => $request->ip(),
            'metadata' => $validated['metadata'] ?? [],
        ], false);

        $devices = DB::table('device_registrations')
            ->where('employee_profile_id', $profile->id)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get();

        $this->createAttendanceAudit($request, 'device_registrations', 'create', 'employee_profiles', $id, 'Registered device for employee.', [], $validated);

        return response()->json([
            'status' => 'success',
            'message' => $result['auto_bound'] ? 'Device bound successfully.' : 'Device registered successfully.',
            'data' => $devices,
        ]);
    }

    public function attendanceHistory(Request $request, int $id)
    {
        $profile = DB::table('employee_profiles')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$profile) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found'], 404);
        }

        $rows = DB::table('employee_attendance')
            ->where('employee_profile_id', $id)
            ->whereNull('deleted_at')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('attendance_date', '>=', $request->query('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('attendance_date', '<=', $request->query('to')))
            ->orderByDesc('attendance_date')
            ->limit(90)
            ->get();

        return response()->json(['status' => 'success', 'data' => $rows]);
    }

    private function employeeDetails(int $profileId): ?array
    {
        $row = DB::table('employee_profiles as ep')
            ->join('users as u', 'u.id', '=', 'ep.user_id')
            ->leftJoin('departments as d', 'd.id', '=', 'ep.department_id')
            ->leftJoin('designations as dg', 'dg.id', '=', 'ep.designation_id')
            ->leftJoin('branches as b', 'b.id', '=', 'ep.branch_id')
            ->leftJoin('shifts as s', 's.id', '=', 'ep.shift_id')
            ->leftJoin('attendance_policies as p', 'p.id', '=', 'ep.attendance_policy_id')
            ->where('ep.id', $profileId)
            ->whereNull('ep.deleted_at')
            ->select([
                'ep.*',
                'u.name',
                'u.email',
                'u.phone_number',
                'u.alternative_email',
                'u.alternative_phone_number',
                'u.whatsapp_number',
                'u.address',
                'u.image',
                'u.role',
                'u.status as user_status',
                'd.name as department_name',
                'dg.name as designation_name',
                'b.name as branch_name',
                's.name as shift_name',
                'p.name as policy_name',
            ])
            ->first();

        if (!$row) {
            return null;
        }

        $devices = DB::table('device_registrations')
            ->where('employee_profile_id', $profileId)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get();

        return [
            'profile' => $row,
            'devices' => $devices,
        ];
    }

    private function validateEmployeeRequest(Request $request, bool $partial): array
    {
        $rules = [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:255',
            'phone_number' => 'sometimes|nullable|string|max:32',
            'alternative_email' => 'sometimes|nullable|email|max:255',
            'alternative_phone_number' => 'sometimes|nullable|string|max:32',
            'whatsapp_number' => 'sometimes|nullable|string|max:32',
            'address' => 'sometimes|nullable|string',
            'password' => $partial ? 'sometimes|nullable|string|min:8' : 'required|string|min:8',
            'status' => 'sometimes|nullable|in:active,inactive',
            'department_id' => 'sometimes|nullable|integer|exists:departments,id',
            'designation_id' => 'sometimes|nullable|integer|exists:designations,id',
            'branch_id' => 'sometimes|nullable|integer|exists:branches,id',
            'shift_id' => 'sometimes|nullable|integer|exists:shifts,id',
            'attendance_policy_id' => 'sometimes|nullable|integer|exists:attendance_policies,id',
            'manager_user_id' => 'sometimes|nullable|integer|exists:users,id',
            'employee_code' => 'sometimes|nullable|string|max:64',
            'employment_type' => 'sometimes|nullable|string|max:40',
            'work_mode' => 'sometimes|nullable|in:office,field,wfh,hybrid',
            'join_date' => 'sometimes|nullable|date',
            'confirmation_date' => 'sometimes|nullable|date',
            'exit_date' => 'sometimes|nullable|date',
            'offline_attendance_enabled' => 'sometimes|nullable|boolean',
            'field_attendance_enabled' => 'sometimes|nullable|boolean',
            'wfh_attendance_enabled' => 'sometimes|nullable|boolean',
            'continuous_tracking_enabled' => 'sometimes|nullable|boolean',
            'face_image_path' => 'sometimes|nullable|string|max:255',
            'face_image' => 'sometimes|file|mimes:jpg,jpeg,png,webp|max:5120',
            'image' => 'sometimes|file|mimes:jpg,jpeg,png,webp|max:5120',
            'image_path' => 'sometimes|nullable|string|max:255',
            'employee_status' => 'sometimes|nullable|in:active,inactive',
            'notes' => 'sometimes|nullable|string',
            'metadata' => 'sometimes|nullable|array',
            'device_id' => 'sometimes|nullable|string|max:120',
            'device_name' => 'sometimes|nullable|string|max:150',
            'device_platform' => 'sometimes|nullable|string|max:40',
            'device_model' => 'sometimes|nullable|string|max:120',
            'os_version' => 'sometimes|nullable|string|max:60',
            'app_version' => 'sometimes|nullable|string|max:60',
        ];

        if ($partial) {
            foreach ($rules as $field => $rule) {
                if (is_string($rule) && !str_starts_with($rule, 'sometimes|')) {
                    $rules[$field] = 'sometimes|' . $rule;
                }
            }
        }

        return $request->validate($rules);
    }

    private function buildUniqueUserSlug(string $name, ?int $ignoreUserId = null): string
    {
        $base = Str::slug($name) ?: 'employee';

        do {
            $slug = $base . '-' . Str::lower(Str::random(12));
            $query = DB::table('users')->where('slug', $slug);
            if ($ignoreUserId) {
                $query->where('id', '!=', $ignoreUserId);
            }
        } while ($query->exists());

        return $slug;
    }

    private function resolveFaceImage(Request $request): ?string
    {
        if (!$request->hasFile('face_image')) {
            return null;
        }

        $saved = $this->storePublicMedia($request->file('face_image'), 'assets/media/images/attendance/faces', 'face');
        return $saved !== false ? $saved : null;
    }

    private function resolveUserImage(Request $request): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $saved = $this->storePublicMedia($request->file('image'), 'assets/media/images/users', 'user');
        return $saved !== false ? $saved : null;
    }
}
