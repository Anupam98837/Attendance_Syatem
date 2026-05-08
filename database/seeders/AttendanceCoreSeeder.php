<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AttendanceCoreSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->seedUsers();
        $setup = $this->seedAttendanceSetup();
        $profiles = $this->seedEmployeeProfiles($users, $setup);
        $this->seedDemoRuntimeData($users, $profiles, $setup);

        [$menus, $privileges] = $this->seedMenusAndPrivileges();
        $this->seedHrRolePrivileges($menus, $privileges);
    }

    private function seedUsers(): array
    {
        return [
            'admin' => $this->findUserByEmail('admin@gmail.com'),
            'hr' => $this->upsertUser(
                email: 'hr@gmail.com',
                name: 'HR Manager',
                role: 'hr',
                roleShort: 'HR',
                password: 'hr@12345',
                phone: '9000000001'
            ),
            'employee' => $this->upsertUser(
                email: 'employee@gmail.com',
                name: 'Rahul Das',
                role: 'employee',
                roleShort: 'EMP',
                password: 'employee@123',
                phone: '9000000002'
            ),
            'priya' => $this->upsertUser(
                email: 'priya.sen@gmail.com',
                name: 'Priya Sen',
                role: 'employee',
                roleShort: 'EMP',
                password: 'employee@123',
                phone: '9000000003'
            ),
            'amit' => $this->upsertUser(
                email: 'amit.roy@gmail.com',
                name: 'Amit Roy',
                role: 'employee',
                roleShort: 'EMP',
                password: 'employee@123',
                phone: '9000000004'
            ),
            'ritu' => $this->upsertUser(
                email: 'ritu.pal@gmail.com',
                name: 'Ritu Pal',
                role: 'employee',
                roleShort: 'EMP',
                password: 'employee@123',
                phone: '9000000005'
            ),
            'arjun' => $this->upsertUser(
                email: 'arjun.ghosh@gmail.com',
                name: 'Arjun Ghosh',
                role: 'employee',
                roleShort: 'EMP',
                password: 'employee@123',
                phone: '9000000006'
            ),
            'sanjay' => $this->upsertUser(
                email: 'sanjay.kumar@gmail.com',
                name: 'Sanjay Kumar',
                role: 'employee',
                roleShort: 'EMP',
                password: 'employee@123',
                phone: '9000000007'
            ),
        ];
    }

    private function seedAttendanceSetup(): array
    {
        $companyId = $this->upsertCompanySettings();

        $departments = [
            'hr' => $this->upsertNamedRecord('departments', 'HR', [
                'code' => 'HR',
                'description' => 'Human resources and workforce operations.',
            ]),
            'sales' => $this->upsertNamedRecord('departments', 'Sales', [
                'code' => 'SALES',
                'description' => 'Sales operations and client acquisition.',
            ]),
            'accounts' => $this->upsertNamedRecord('departments', 'Accounts', [
                'code' => 'ACC',
                'description' => 'Finance and accounting team.',
            ]),
            'development' => $this->upsertNamedRecord('departments', 'Development', [
                'code' => 'DEV',
                'description' => 'Product engineering and development team.',
            ]),
            'operations' => $this->upsertNamedRecord('departments', 'Operations', [
                'code' => 'OPS',
                'description' => 'Operations and administration team.',
            ]),
            'field_staff' => $this->upsertNamedRecord('departments', 'Field Staff', [
                'code' => 'FIELD',
                'description' => 'Mobile field workforce.',
            ]),
        ];

        $designations = [
            'hr_executive' => $this->upsertNamedRecord('designations', 'HR Executive', [
                'code' => 'HR-EXEC',
                'description' => 'Handles HR operations and attendance approvals.',
            ]),
            'chief_executive_officer' => $this->upsertNamedRecord('designations', 'Chief Executive Officer', [
                'code' => 'CEO',
                'description' => 'Top-level executive responsible for company direction.',
            ]),
            'chief_operating_officer' => $this->upsertNamedRecord('designations', 'Chief Operating Officer', [
                'code' => 'COO',
                'description' => 'Leads business operations.',
            ]),
            'chief_financial_officer' => $this->upsertNamedRecord('designations', 'Chief Financial Officer', [
                'code' => 'CFO',
                'description' => 'Leads financial planning and control.',
            ]),
            'chief_technology_officer' => $this->upsertNamedRecord('designations', 'Chief Technology Officer', [
                'code' => 'CTO',
                'description' => 'Leads technology strategy and engineering direction.',
            ]),
            'vice_president' => $this->upsertNamedRecord('designations', 'Vice President', [
                'code' => 'VP',
                'description' => 'Senior business or functional leader.',
            ]),
            'director' => $this->upsertNamedRecord('designations', 'Director', [
                'code' => 'DIR',
                'description' => 'Department or function leader.',
            ]),
            'senior_manager' => $this->upsertNamedRecord('designations', 'Senior Manager', [
                'code' => 'SR-MGR',
                'description' => 'Senior team or department manager.',
            ]),
            'software_developer' => $this->upsertNamedRecord('designations', 'Software Developer', [
                'code' => 'DEV-SW',
                'description' => 'Engineering team member.',
            ]),
            'senior_software_engineer' => $this->upsertNamedRecord('designations', 'Senior Software Engineer', [
                'code' => 'SR-SWE',
                'description' => 'Senior engineering contributor.',
            ]),
            'software_engineer' => $this->upsertNamedRecord('designations', 'Software Engineer', [
                'code' => 'SWE',
                'description' => 'Engineering contributor.',
            ]),
            'qa_engineer' => $this->upsertNamedRecord('designations', 'QA Engineer', [
                'code' => 'QA',
                'description' => 'Quality assurance and testing contributor.',
            ]),
            'product_manager' => $this->upsertNamedRecord('designations', 'Product Manager', [
                'code' => 'PM',
                'description' => 'Owns product planning and delivery.',
            ]),
            'business_analyst' => $this->upsertNamedRecord('designations', 'Business Analyst', [
                'code' => 'BA',
                'description' => 'Bridges business and implementation needs.',
            ]),
            'team_lead' => $this->upsertNamedRecord('designations', 'Team Lead', [
                'code' => 'TL',
                'description' => 'Leads a delivery team.',
            ]),
            'sales_officer' => $this->upsertNamedRecord('designations', 'Sales Officer', [
                'code' => 'SALES-OFF',
                'description' => 'Field or branch sales employee.',
            ]),
            'sales_executive' => $this->upsertNamedRecord('designations', 'Sales Executive', [
                'code' => 'SALES-EXEC',
                'description' => 'Sales contributor handling leads and conversions.',
            ]),
            'marketing_executive' => $this->upsertNamedRecord('designations', 'Marketing Executive', [
                'code' => 'MKT-EXEC',
                'description' => 'Marketing contributor handling campaigns and brand work.',
            ]),
            'manager' => $this->upsertNamedRecord('designations', 'Manager', [
                'code' => 'MGR',
                'description' => 'Team or branch manager.',
            ]),
            'assistant_manager' => $this->upsertNamedRecord('designations', 'Assistant Manager', [
                'code' => 'AST-MGR',
                'description' => 'Supports business or team management.',
            ]),
            'accounts_executive' => $this->upsertNamedRecord('designations', 'Accounts Executive', [
                'code' => 'ACC-EXEC',
                'description' => 'Handles finance operations and records.',
            ]),
            'admin_executive' => $this->upsertNamedRecord('designations', 'Admin Executive', [
                'code' => 'ADMIN-EXEC',
                'description' => 'Handles administrative operations.',
            ]),
            'customer_support_executive' => $this->upsertNamedRecord('designations', 'Customer Support Executive', [
                'code' => 'CS-EXEC',
                'description' => 'Handles customer support operations.',
            ]),
            'operations_executive' => $this->upsertNamedRecord('designations', 'Operations Executive', [
                'code' => 'OPS-EXEC',
                'description' => 'Handles daily business operations.',
            ]),
            'security_guard' => $this->upsertNamedRecord('designations', 'Security Guard', [
                'code' => 'SEC',
                'description' => 'Security and facility attendance staff.',
            ]),
            'field_executive' => $this->upsertNamedRecord('designations', 'Field Executive', [
                'code' => 'FIELD-EXEC',
                'description' => 'Travelling employee with field attendance.',
            ]),
            'receptionist' => $this->upsertNamedRecord('designations', 'Receptionist', [
                'code' => 'RECEP',
                'description' => 'Front-desk and visitor coordination role.',
            ]),
            'data_entry_operator' => $this->upsertNamedRecord('designations', 'Data Entry Operator', [
                'code' => 'DEO',
                'description' => 'Handles data entry and clerical tasks.',
            ]),
            'intern' => $this->upsertNamedRecord('designations', 'Intern', [
                'code' => 'INTERN',
                'description' => 'Trainee or internship role.',
            ]),
        ];

        $branchId = $this->upsertNamedRecord('branches', 'Kolkata Office', [
            'code' => 'KOL',
            'address' => 'Salt Lake, Kolkata',
            'city' => 'Kolkata',
            'state' => 'West Bengal',
            'country' => 'India',
            'postal_code' => '700091',
            'latitude' => 22.5726,
            'longitude' => 88.3639,
            'geofence_radius_meters' => 100,
            'wifi_only' => 1,
            'allow_mobile_data' => 0,
            'allow_outside_geofence' => 0,
            'metadata' => json_encode(['mode' => 'office_primary']),
        ]);

        $this->upsertBranchNetwork($branchId, 'Office LAN', '192.168.1.0/24', 'wifi');
        $this->upsertBranchNetwork($branchId, 'Office Static WAN', '49.36.120.10', 'wifi');
        $this->upsertBranchNetwork($branchId, 'Localhost IPv4', '127.0.0.1', 'public_ip');
        $this->upsertBranchNetwork($branchId, 'Localhost IPv6', '::1', 'public_ip');

        $shifts = [
            'general' => $this->upsertNamedRecord('shifts', 'General Shift', [
                'code' => 'GEN',
                'start_time' => '10:00:00',
                'end_time' => '18:00:00',
                'allow_cross_day' => 0,
                'grace_minutes' => 10,
                'late_after_time' => '10:10:00',
                'half_day_working_minutes' => 240,
                'full_day_working_minutes' => 480,
                'minimum_working_minutes' => 240,
                'overtime_after_minutes' => 480,
                'early_checkout_before_minutes' => 30,
                'break_minutes' => 60,
                'week_days' => json_encode(['mon', 'tue', 'wed', 'thu', 'fri', 'sat']),
            ]),
            'morning' => $this->upsertNamedRecord('shifts', 'Morning Shift', [
                'code' => 'MORN',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'allow_cross_day' => 0,
                'grace_minutes' => 10,
                'late_after_time' => '08:10:00',
                'half_day_working_minutes' => 240,
                'full_day_working_minutes' => 480,
                'minimum_working_minutes' => 240,
                'overtime_after_minutes' => 480,
                'early_checkout_before_minutes' => 20,
                'break_minutes' => 45,
                'week_days' => json_encode(['mon', 'tue', 'wed', 'thu', 'fri', 'sat']),
            ]),
            'night' => $this->upsertNamedRecord('shifts', 'Night Shift', [
                'code' => 'NIGHT',
                'start_time' => '21:00:00',
                'end_time' => '06:00:00',
                'allow_cross_day' => 1,
                'grace_minutes' => 15,
                'late_after_time' => '21:15:00',
                'half_day_working_minutes' => 240,
                'full_day_working_minutes' => 480,
                'minimum_working_minutes' => 240,
                'overtime_after_minutes' => 480,
                'early_checkout_before_minutes' => 30,
                'break_minutes' => 60,
                'week_days' => json_encode(['mon', 'tue', 'wed', 'thu', 'fri', 'sat']),
            ]),
        ];

        $policies = [
            'office' => $this->upsertNamedRecord('attendance_policies', 'Office Hybrid Policy', [
                'code' => 'OFFICE-HYB',
                'description' => 'GPS + selfie + device binding + Wi-Fi/IP + offline review for office staff.',
                'gps_required' => 1,
                'selfie_required' => 1,
                'checkout_selfie_required' => 1,
                'face_verification_mode' => 'optional',
                'liveness_required' => 0,
                'offline_attendance_allowed' => 1,
                'offline_sync_limit_hours' => 24,
                'offline_sync_limit_minutes' => 1440,
                'multiple_punch_allowed' => 0,
                'geofence_required' => 1,
                'outside_location_allowed' => 0,
                'outside_location_requires_approval' => 1,
                'device_binding_required' => 1,
                'wifi_ip_restriction_required' => 1,
                'allow_mobile_data' => 0,
                'qr_attendance_enabled' => 0,
                'mark_gps_missing_as_exception' => 1,
                'auto_approve_clean_records' => 1,
                'time_drift_tolerance_minutes' => 10,
                'max_offline_records_per_day' => 4,
                'continuous_tracking_interval_seconds' => 180,
                'allow_field_attendance' => 0,
                'allow_wfh_attendance' => 0,
                'require_work_note_for_wfh' => 0,
                'allowed_punch_types' => json_encode(['check_in', 'check_out']),
            ]),
            'field' => $this->upsertNamedRecord('attendance_policies', 'Field Attendance Policy', [
                'code' => 'FIELD-HYB',
                'description' => 'GPS + selfie + offline sync for field employees with optional location approval.',
                'gps_required' => 1,
                'selfie_required' => 1,
                'checkout_selfie_required' => 0,
                'face_verification_mode' => 'optional',
                'liveness_required' => 0,
                'offline_attendance_allowed' => 1,
                'offline_sync_limit_hours' => 24,
                'offline_sync_limit_minutes' => 1440,
                'multiple_punch_allowed' => 0,
                'geofence_required' => 0,
                'outside_location_allowed' => 1,
                'outside_location_requires_approval' => 0,
                'device_binding_required' => 1,
                'wifi_ip_restriction_required' => 0,
                'allow_mobile_data' => 1,
                'qr_attendance_enabled' => 0,
                'mark_gps_missing_as_exception' => 1,
                'auto_approve_clean_records' => 1,
                'time_drift_tolerance_minutes' => 15,
                'max_offline_records_per_day' => 6,
                'continuous_tracking_interval_seconds' => 300,
                'allow_field_attendance' => 1,
                'allow_wfh_attendance' => 0,
                'require_work_note_for_wfh' => 0,
                'allowed_punch_types' => json_encode(['check_in', 'check_out']),
            ]),
            'wfh' => $this->upsertNamedRecord('attendance_policies', 'WFH Attendance Policy', [
                'code' => 'WFH-HYB',
                'description' => 'Selfie + note + hybrid check-in/check-out for remote staff.',
                'gps_required' => 0,
                'selfie_required' => 1,
                'checkout_selfie_required' => 0,
                'face_verification_mode' => 'optional',
                'liveness_required' => 0,
                'offline_attendance_allowed' => 1,
                'offline_sync_limit_hours' => 24,
                'offline_sync_limit_minutes' => 1440,
                'multiple_punch_allowed' => 0,
                'geofence_required' => 0,
                'outside_location_allowed' => 1,
                'outside_location_requires_approval' => 0,
                'device_binding_required' => 1,
                'wifi_ip_restriction_required' => 0,
                'allow_mobile_data' => 1,
                'qr_attendance_enabled' => 0,
                'mark_gps_missing_as_exception' => 0,
                'auto_approve_clean_records' => 0,
                'time_drift_tolerance_minutes' => 20,
                'max_offline_records_per_day' => 4,
                'continuous_tracking_interval_seconds' => 0,
                'allow_field_attendance' => 0,
                'allow_wfh_attendance' => 1,
                'require_work_note_for_wfh' => 1,
                'allowed_punch_types' => json_encode(['check_in', 'check_out']),
            ]),
        ];

        $leaveTypes = [
            'casual' => $this->upsertNamedRecord('leave_types', 'Casual Leave', [
                'code' => 'CL',
                'days_allowed' => 12,
                'is_paid' => 1,
                'requires_approval' => 1,
                'requires_document' => 0,
                'description' => 'General casual leave allowance.',
            ]),
            'sick' => $this->upsertNamedRecord('leave_types', 'Sick Leave', [
                'code' => 'SL',
                'days_allowed' => 10,
                'is_paid' => 1,
                'requires_approval' => 1,
                'requires_document' => 0,
                'description' => 'Medical or sickness-related leave.',
            ]),
            'earned' => $this->upsertNamedRecord('leave_types', 'Earned Leave', [
                'code' => 'EL',
                'days_allowed' => 18,
                'is_paid' => 1,
                'requires_approval' => 1,
                'requires_document' => 0,
                'description' => 'Accrued annual leave.',
            ]),
        ];

        return [
            'company_id' => $companyId,
            'departments' => $departments,
            'designations' => $designations,
            'branch_id' => $branchId,
            'shifts' => $shifts,
            'policies' => $policies,
            'leave_types' => $leaveTypes,
        ];
    }

    private function seedEmployeeProfiles(array $users, array $setup): array
    {
        $hrUserId = $users['hr']->id ?? null;
        $employeeUserId = $users['employee']->id ?? null;
        $profiles = [];

        if ($hrUserId) {
            $this->updateUserOrgMap($hrUserId, [
                'department_id' => $setup['departments']['hr'],
                'designation_id' => $setup['designations']['hr_executive'],
                'branch_id' => $setup['branch_id'],
            ]);

            $this->upsertEmployeeProfile($hrUserId, [
                'employee_code' => 'HR001',
                'department_id' => $setup['departments']['hr'],
                'designation_id' => $setup['designations']['hr_executive'],
                'branch_id' => $setup['branch_id'],
                'shift_id' => $setup['shifts']['general'],
                'attendance_policy_id' => $setup['policies']['office'],
                'employment_type' => 'permanent',
                'work_mode' => 'office',
                'join_date' => now()->subYear()->toDateString(),
                'offline_attendance_enabled' => 1,
                'field_attendance_enabled' => 0,
                'wfh_attendance_enabled' => 0,
                'continuous_tracking_enabled' => 1,
                'status' => 'active',
                'notes' => 'Seeded HR operator profile.',
            ]);
        }

        if ($employeeUserId) {
            $this->updateUserOrgMap($employeeUserId, [
                'department_id' => $setup['departments']['development'],
                'designation_id' => $setup['designations']['software_developer'],
                'branch_id' => $setup['branch_id'],
            ]);

            $profileId = $this->upsertEmployeeProfile($employeeUserId, [
                'employee_code' => 'EMP001',
                'department_id' => $setup['departments']['development'],
                'designation_id' => $setup['designations']['software_developer'],
                'branch_id' => $setup['branch_id'],
                'shift_id' => $setup['shifts']['general'],
                'attendance_policy_id' => $setup['policies']['office'],
                'manager_user_id' => $hrUserId,
                'employment_type' => 'permanent',
                'work_mode' => 'office',
                'join_date' => now()->subMonths(6)->toDateString(),
                'offline_attendance_enabled' => 1,
                'field_attendance_enabled' => 0,
                'wfh_attendance_enabled' => 0,
                'continuous_tracking_enabled' => 1,
                'status' => 'active',
                'notes' => 'Seeded employee mobile attendance profile.',
            ]);

            $this->upsertDeviceRegistration($employeeUserId, $profileId, 'seeded-employee-device-001', [
                'device_name' => 'Android Seed Device',
                'device_platform' => 'android',
                'device_model' => 'Pixel',
                'os_version' => '14',
                'app_version' => '1.0.0',
            ]);

            $profiles['employee'] = $profileId;
        }

        $employeeDefinitions = [
            'priya' => [
                'employee_code' => 'EMP002',
                'department_id' => $setup['departments']['accounts'],
                'designation_id' => $setup['designations']['accounts_executive'],
                'branch_id' => $setup['branch_id'],
                'shift_id' => $setup['shifts']['general'],
                'attendance_policy_id' => $setup['policies']['office'],
                'manager_user_id' => $hrUserId,
                'employment_type' => 'permanent',
                'work_mode' => 'office',
                'join_date' => now()->subMonths(18)->toDateString(),
                'offline_attendance_enabled' => 1,
                'field_attendance_enabled' => 0,
                'wfh_attendance_enabled' => 0,
                'continuous_tracking_enabled' => 1,
                'status' => 'active',
                'notes' => 'Seeded on-time office employee.',
                'device' => ['seeded-employee-device-002', 'iPhone 15', 'ios', 'iPhone', '17.4', '1.0.0'],
            ],
            'amit' => [
                'employee_code' => 'EMP003',
                'department_id' => $setup['departments']['field_staff'],
                'designation_id' => $setup['designations']['field_executive'],
                'branch_id' => $setup['branch_id'],
                'shift_id' => $setup['shifts']['morning'],
                'attendance_policy_id' => $setup['policies']['field'],
                'manager_user_id' => $hrUserId,
                'employment_type' => 'permanent',
                'work_mode' => 'field',
                'join_date' => now()->subMonths(10)->toDateString(),
                'offline_attendance_enabled' => 1,
                'field_attendance_enabled' => 1,
                'wfh_attendance_enabled' => 0,
                'continuous_tracking_enabled' => 1,
                'status' => 'active',
                'notes' => 'Seeded field employee.',
                'device' => ['seeded-employee-device-003', 'Samsung Field Tab', 'android', 'Galaxy Tab', '14', '1.0.0'],
            ],
            'ritu' => [
                'employee_code' => 'EMP004',
                'department_id' => $setup['departments']['operations'],
                'designation_id' => $setup['designations']['operations_executive'],
                'branch_id' => $setup['branch_id'],
                'shift_id' => $setup['shifts']['general'],
                'attendance_policy_id' => $setup['policies']['wfh'],
                'manager_user_id' => $hrUserId,
                'employment_type' => 'permanent',
                'work_mode' => 'wfh',
                'join_date' => now()->subMonths(14)->toDateString(),
                'offline_attendance_enabled' => 1,
                'field_attendance_enabled' => 0,
                'wfh_attendance_enabled' => 1,
                'continuous_tracking_enabled' => 0,
                'status' => 'active',
                'notes' => 'Seeded work from home employee.',
                'device' => ['seeded-employee-device-004', 'MacBook Air', 'ios', 'MacBook Air', '14.4', '1.0.0'],
            ],
            'arjun' => [
                'employee_code' => 'EMP005',
                'department_id' => $setup['departments']['sales'],
                'designation_id' => $setup['designations']['sales_executive'],
                'branch_id' => $setup['branch_id'],
                'shift_id' => $setup['shifts']['general'],
                'attendance_policy_id' => $setup['policies']['office'],
                'manager_user_id' => $hrUserId,
                'employment_type' => 'permanent',
                'work_mode' => 'office',
                'join_date' => now()->subMonths(4)->toDateString(),
                'offline_attendance_enabled' => 1,
                'field_attendance_enabled' => 0,
                'wfh_attendance_enabled' => 0,
                'continuous_tracking_enabled' => 1,
                'status' => 'active',
                'notes' => 'Seeded employee with approval-needed record.',
                'device' => ['seeded-employee-device-005', 'Moto G', 'android', 'Moto G', '13', '1.0.0'],
            ],
            'sanjay' => [
                'employee_code' => 'EMP006',
                'department_id' => $setup['departments']['development'],
                'designation_id' => $setup['designations']['qa_engineer'],
                'branch_id' => $setup['branch_id'],
                'shift_id' => $setup['shifts']['general'],
                'attendance_policy_id' => $setup['policies']['office'],
                'manager_user_id' => $hrUserId,
                'employment_type' => 'probation',
                'work_mode' => 'office',
                'join_date' => now()->subMonths(2)->toDateString(),
                'offline_attendance_enabled' => 1,
                'field_attendance_enabled' => 0,
                'wfh_attendance_enabled' => 0,
                'continuous_tracking_enabled' => 0,
                'status' => 'active',
                'notes' => 'Seeded employee left unmarked for today.',
                'device' => ['seeded-employee-device-006', 'OnePlus 12', 'android', 'OnePlus', '14', '1.0.0'],
            ],
        ];

        foreach ($employeeDefinitions as $key => $definition) {
            $user = $users[$key] ?? null;
            if (!$user?->id) {
                continue;
            }

            $this->updateUserOrgMap((int) $user->id, [
                'department_id' => $definition['department_id'],
                'designation_id' => $definition['designation_id'],
                'branch_id' => $definition['branch_id'],
            ]);

            $device = $definition['device'];
            unset($definition['device']);

            $profileId = $this->upsertEmployeeProfile((int) $user->id, $definition);
            $profiles[$key] = $profileId;

            $this->upsertDeviceRegistration((int) $user->id, $profileId, $device[0], [
                'device_name' => $device[1],
                'device_platform' => $device[2],
                'device_model' => $device[3],
                'os_version' => $device[4],
                'app_version' => $device[5],
            ]);
        }

        return $profiles;
    }

    private function seedMenusAndPrivileges(): array
    {
        $menuConfig = [
            'attendance_setup' => [
                'name' => 'Attendance Setup',
                'href' => null,
                'description' => null,
                'icon' => 'fa-solid fa-gears',
                'header' => true,
                'position' => 1,
                'actions' => [],
            ],
            'workforce' => [
                'name' => 'Workforce',
                'href' => null,
                'description' => null,
                'icon' => 'fa-solid fa-users-gear',
                'header' => true,
                'position' => 2,
                'actions' => [],
            ],
            'attendance_operations' => [
                'name' => 'Attendance Operations',
                'href' => null,
                'description' => null,
                'icon' => 'fa-solid fa-clock',
                'header' => true,
                'position' => 3,
                'actions' => [],
            ],
            'access_control' => [
                'name' => 'Access Control',
                'href' => null,
                'description' => null,
                'icon' => 'fa-solid fa-shield-halved',
                'header' => true,
                'position' => 4,
                'actions' => [],
            ],
            'company_settings' => [
                'name' => 'Company Settings',
                'href' => 'attendance/company',
                'description' => 'Manage company-wide attendance defaults.',
                'icon' => 'fa-solid fa-building',
                'header' => false,
                'position' => 1,
                'parent' => 'attendance_setup',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'branches' => [
                'name' => 'Branches & Locations',
                'href' => 'attendance/branches',
                'description' => 'Manage offices, geofence, and Wi-Fi/IP rules.',
                'icon' => 'fa-solid fa-location-dot',
                'header' => false,
                'position' => 2,
                'parent' => 'attendance_setup',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'branch_networks' => [
                'name' => 'Wi-Fi / IP Rules',
                'href' => 'attendance/branch-networks',
                'description' => 'Manage allowed office Wi-Fi and IP patterns branch-wise.',
                'icon' => 'fa-solid fa-wifi',
                'header' => false,
                'position' => 3,
                'parent' => 'attendance_setup',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'departments' => [
                'name' => 'Departments',
                'href' => 'attendance/departments',
                'description' => 'Manage department master data.',
                'icon' => 'fa-solid fa-sitemap',
                'header' => false,
                'position' => 4,
                'parent' => 'attendance_setup',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'designations' => [
                'name' => 'Designations',
                'href' => 'attendance/designations',
                'description' => 'Manage employee designation master data.',
                'icon' => 'fa-solid fa-id-badge',
                'header' => false,
                'position' => 5,
                'parent' => 'attendance_setup',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'shifts' => [
                'name' => 'Shifts',
                'href' => 'attendance/shifts',
                'description' => 'Configure shifts and attendance timing rules.',
                'icon' => 'fa-solid fa-business-time',
                'header' => false,
                'position' => 6,
                'parent' => 'attendance_setup',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'attendance_policies' => [
                'name' => 'Attendance Policies',
                'href' => 'attendance/policies',
                'description' => 'Configure GPS, selfie, offline, device, and Wi-Fi rules.',
                'icon' => 'fa-solid fa-sliders',
                'header' => false,
                'position' => 7,
                'parent' => 'attendance_setup',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'holidays' => [
                'name' => 'Holidays',
                'href' => 'attendance/holidays',
                'description' => 'Manage holiday calendars.',
                'icon' => 'fa-solid fa-calendar-days',
                'header' => false,
                'position' => 8,
                'parent' => 'attendance_setup',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'leave_types' => [
                'name' => 'Leave Types',
                'href' => 'attendance/leave-types',
                'description' => 'Manage leave categories and limits.',
                'icon' => 'fa-solid fa-plane-departure',
                'header' => false,
                'position' => 9,
                'parent' => 'attendance_setup',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'employees' => [
                'name' => 'Employees',
                'href' => 'attendance/employees',
                'description' => 'Manage attendance employee profiles.',
                'icon' => 'fa-solid fa-users',
                'header' => false,
                'position' => 1,
                'parent' => 'workforce',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'manage_users' => [
                'name' => 'Manage Users',
                'href' => 'user/manage',
                'description' => 'Create and manage user accounts.',
                'icon' => 'fa-solid fa-user-group',
                'header' => false,
                'position' => 2,
                'parent' => 'workforce',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'today_attendance' => [
                'name' => 'Today Attendance',
                'href' => 'attendance/today',
                'description' => 'Review live attendance and check-in status.',
                'icon' => 'fa-solid fa-clock',
                'header' => false,
                'position' => 1,
                'parent' => 'attendance_operations',
                'actions' => ['view'],
            ],
            'attendance_register' => [
                'name' => 'Attendance Register',
                'href' => 'attendance/records',
                'description' => 'Review the core attendance ledger across dates and states.',
                'icon' => 'fa-solid fa-table-list',
                'header' => false,
                'position' => 2,
                'parent' => 'attendance_operations',
                'actions' => ['view'],
            ],
            'monthly_attendance' => [
                'name' => 'Monthly Attendance',
                'href' => 'attendance/monthly',
                'description' => 'Review monthly summaries and history.',
                'icon' => 'fa-solid fa-calendar-check',
                'header' => false,
                'position' => 3,
                'parent' => 'attendance_operations',
                'actions' => ['view'],
            ],
            'pending_approvals' => [
                'name' => 'Pending Approvals',
                'href' => 'attendance/pending-approvals',
                'description' => 'Review geofence, offline, and manual correction approvals.',
                'icon' => 'fa-solid fa-user-check',
                'header' => false,
                'position' => 4,
                'parent' => 'attendance_operations',
                'actions' => ['view', 'approve', 'edit'],
            ],
            'attendance_reports' => [
                'name' => 'Attendance Reports',
                'href' => 'attendance/reports',
                'description' => 'Export daily, monthly, payroll, and exception reports.',
                'icon' => 'fa-solid fa-chart-column',
                'header' => false,
                'position' => 5,
                'parent' => 'attendance_operations',
                'actions' => ['view', 'export'],
            ],
            'offline_sync_logs' => [
                'name' => 'Offline Sync Logs',
                'href' => 'attendance/offline-sync-logs',
                'description' => 'Monitor offline queue processing and sync failures.',
                'icon' => 'fa-solid fa-cloud-arrow-up',
                'header' => false,
                'position' => 6,
                'parent' => 'attendance_operations',
                'actions' => ['view'],
            ],
            'location_exceptions' => [
                'name' => 'Location Exceptions',
                'href' => 'attendance/location-exceptions',
                'description' => 'Review GPS, geofence, and Wi-Fi/IP violations.',
                'icon' => 'fa-solid fa-triangle-exclamation',
                'header' => false,
                'position' => 7,
                'parent' => 'attendance_operations',
                'actions' => ['view'],
            ],
            'leave_management' => [
                'name' => 'Leave Management',
                'href' => 'attendance/leaves',
                'description' => 'Review employee leave requests.',
                'icon' => 'fa-solid fa-file-signature',
                'header' => false,
                'position' => 8,
                'parent' => 'attendance_operations',
                'actions' => ['view', 'approve'],
            ],
            'employee_mobile' => [
                'name' => 'Employee App Blueprint',
                'href' => 'attendance/employee-mobile',
                'description' => 'Preview the employee attendance experience before mobile implementation.',
                'icon' => 'fa-solid fa-mobile-screen-button',
                'header' => false,
                'position' => 9,
                'parent' => 'attendance_operations',
                'actions' => ['view'],
            ],
            'dashboard_menu_manage' => [
                'name' => 'Manage Dashboard Menu',
                'href' => 'dashboard-menu/manage',
                'description' => 'Maintain sidebar modules.',
                'icon' => 'fa-solid fa-puzzle-piece',
                'header' => false,
                'position' => 1,
                'parent' => 'access_control',
                'actions' => ['view'],
            ],
            'page_privilege_manage' => [
                'name' => 'Manage Page Privileges',
                'href' => 'page-privilege/manage',
                'description' => 'Review page-level actions.',
                'icon' => 'fa-solid fa-lock',
                'header' => false,
                'position' => 2,
                'parent' => 'access_control',
                'actions' => ['view'],
            ],
            'role_privilege_manage' => [
                'name' => 'Assign Role Privileges',
                'href' => 'role-privileges/manage',
                'description' => 'Map actions to roles.',
                'icon' => 'fa-solid fa-user-shield',
                'header' => false,
                'position' => 3,
                'parent' => 'access_control',
                'actions' => ['view'],
            ],
            'user_privilege_manage' => [
                'name' => 'Assign User Privileges',
                'href' => 'user-privileges/manage',
                'description' => 'Grant user-specific access.',
                'icon' => 'fa-solid fa-user-lock',
                'header' => false,
                'position' => 4,
                'parent' => 'access_control',
                'actions' => ['view'],
            ],
        ];

        $menus = [];
        foreach ($menuConfig as $key => $config) {
            $menus[$key] = $this->upsertMenu(
                name: $config['name'],
                href: $config['href'],
                description: $config['description'],
                icon: $config['icon'],
                isHeader: $config['header'],
                position: $config['position'],
                parentId: !empty($config['parent']) ? (int) $menus[$config['parent']]['id'] : null
            );
        }

        $privileges = [];
        foreach ($menuConfig as $key => $config) {
            foreach ($config['actions'] as $action) {
                $privileges[$key][$action] = $this->upsertPrivilege(
                    (int) $menus[$key]['id'],
                    $action,
                    $config['name'] . ' - ' . Str::headline($action)
                );
            }
        }

        return [$menus, $privileges];
    }

    private function seedHrRolePrivileges(array $menus, array $privileges): void
    {
        $definition = [
            'workforce' => [
                'employees' => ['view', 'create', 'edit'],
                'manage_users' => ['view', 'create', 'edit'],
            ],
            'attendance_operations' => [
                'today_attendance' => ['view'],
                'attendance_register' => ['view'],
                'monthly_attendance' => ['view'],
                'pending_approvals' => ['view', 'approve', 'edit'],
                'attendance_reports' => ['view', 'export'],
                'offline_sync_logs' => ['view'],
                'location_exceptions' => ['view'],
                'leave_management' => ['view', 'approve'],
                'employee_mobile' => ['view'],
            ],
        ];

        $tree = [];
        foreach ($definition as $headerKey => $children) {
            $treeChildren = [];

            foreach ($children as $pageKey => $actions) {
                $pagePrivileges = [];
                foreach ($actions as $action) {
                    if (!empty($privileges[$pageKey][$action]['id'])) {
                        $pagePrivileges[] = [
                            'id' => (int) $privileges[$pageKey][$action]['id'],
                            'action' => $action,
                        ];
                    }
                }

                $treeChildren[] = [
                    'id' => (int) $menus[$pageKey]['id'],
                    'type' => 'page',
                    'privileges' => $pagePrivileges,
                ];
            }

            $tree[] = [
                'id' => (int) $menus[$headerKey]['id'],
                'type' => 'header',
                'children' => $treeChildren,
            ];
        }

        $existing = DB::table('role_privileges')->where('role', 'hr')->first();
        $payload = [
            'privileges' => json_encode($tree, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'assigned_by' => $this->findUserByEmail('admin@gmail.com')?->id,
            'created_at_ip' => '127.0.0.1',
            'updated_at' => now(),
            'deleted_at' => null,
        ];

        if ($existing) {
            DB::table('role_privileges')->where('id', $existing->id)->update($payload);
            return;
        }

        DB::table('role_privileges')->insert($payload + [
            'uuid' => (string) Str::uuid(),
            'role' => 'hr',
            'created_at' => now(),
        ]);
    }

    private function findUserByEmail(string $email): ?object
    {
        return DB::table('users')->where('email', $email)->whereNull('deleted_at')->first();
    }

    private function upsertUser(
        string $email,
        string $name,
        string $role,
        string $roleShort,
        string $password,
        ?string $phone = null
    ): object {
        $existing = DB::table('users')->where('email', $email)->first();

        $payload = [
            'name' => $name,
            'slug' => $existing->slug ?? (Str::slug($name) . '-' . Str::lower(Str::random(8))),
            'phone_number' => $phone ?: ($existing->phone_number ?? null),
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'address' => ucfirst($role) . ' account',
            'role' => $role,
            'role_short_form' => $roleShort,
            'status' => 'active',
            'remember_token' => Str::random(10),
            'created_at_ip' => '127.0.0.1',
            'metadata' => json_encode([
                'seeded' => true,
                'module' => 'attendance_core',
            ]),
            'updated_at' => now(),
            'deleted_at' => null,
        ];

        if ($existing) {
            DB::table('users')->where('id', $existing->id)->update($payload);
            return DB::table('users')->where('id', $existing->id)->first();
        }

        $id = DB::table('users')->insertGetId($payload + [
            'uuid' => (string) Str::uuid(),
            'email' => $email,
            'created_at' => now(),
        ]);

        return DB::table('users')->where('id', $id)->first();
    }

    private function upsertCompanySettings(): int
    {
        $existing = DB::table('company_settings')->whereNull('deleted_at')->orderBy('id')->first();

        $payload = [
            'company_name' => 'ABC Pvt Ltd',
            'legal_name' => 'ABC Private Limited',
            'company_code' => 'ABC-ATTEND',
            'timezone' => 'Asia/Kolkata',
            'working_days' => json_encode(['mon', 'tue', 'wed', 'thu', 'fri', 'sat']),
            'weekly_offs' => json_encode(['sun']),
            'attendance_mode' => 'online_offline_hybrid',
            'default_grace_time_minutes' => 10,
            'offline_sync_limit_hours' => 24,
            'default_currency' => 'INR',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'metadata' => json_encode(['seeded' => true]),
            'status' => 'active',
            'updated_at' => now(),
            'deleted_at' => null,
        ];

        if ($existing) {
            DB::table('company_settings')->where('id', $existing->id)->update($payload);
            return (int) $existing->id;
        }

        return (int) DB::table('company_settings')->insertGetId($payload + [
            'uuid' => (string) Str::uuid(),
            'created_at' => now(),
        ]);
    }

    private function upsertNamedRecord(string $table, string $name, array $payload): int
    {
        $existing = DB::table($table)->where('name', $name)->first();

        $data = array_merge($payload, [
            'status' => $payload['status'] ?? 'active',
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        if ($existing) {
            DB::table($table)->where('id', $existing->id)->update($data);
            return (int) $existing->id;
        }

        return (int) DB::table($table)->insertGetId($data + [
            'uuid' => (string) Str::uuid(),
            'name' => $name,
            'created_at' => now(),
        ]);
    }

    private function upsertBranchNetwork(int $branchId, string $label, string $ipPattern, string $networkType): void
    {
        $existing = DB::table('branch_allowed_networks')
            ->where('branch_id', $branchId)
            ->where('ip_pattern', $ipPattern)
            ->first();

        $payload = [
            'label' => $label,
            'network_type' => $networkType,
            'is_active' => 1,
            'notes' => 'Seeded allowed office network.',
            'metadata' => json_encode(['seeded' => true]),
            'updated_at' => now(),
            'deleted_at' => null,
        ];

        if ($existing) {
            DB::table('branch_allowed_networks')->where('id', $existing->id)->update($payload);
            return;
        }

        DB::table('branch_allowed_networks')->insert($payload + [
            'uuid' => (string) Str::uuid(),
            'branch_id' => $branchId,
            'ip_pattern' => $ipPattern,
            'created_at' => now(),
        ]);
    }

    private function updateUserOrgMap(int $userId, array $payload): void
    {
        DB::table('users')->where('id', $userId)->update($payload + ['updated_at' => now()]);
    }

    private function upsertEmployeeProfile(int $userId, array $payload): int
    {
        $existing = DB::table('employee_profiles')->where('user_id', $userId)->first();

        $data = $payload + [
            'updated_at' => now(),
            'deleted_at' => null,
        ];

        if ($existing) {
            DB::table('employee_profiles')->where('id', $existing->id)->update($data);
            return (int) $existing->id;
        }

        return (int) DB::table('employee_profiles')->insertGetId($data + [
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'created_at' => now(),
        ]);
    }

    private function upsertDeviceRegistration(int $userId, int $profileId, string $deviceId, array $payload): void
    {
        $existing = DB::table('device_registrations')
            ->where('user_id', $userId)
            ->where('device_id', $deviceId)
            ->first();

        $data = $payload + [
            'employee_profile_id' => $profileId,
            'last_ip' => '127.0.0.1',
            'bound_at' => now(),
            'last_seen_at' => now(),
            'is_active' => 1,
            'metadata' => json_encode(['seeded' => true]),
            'updated_at' => now(),
            'deleted_at' => null,
        ];

        if ($existing) {
            DB::table('device_registrations')->where('id', $existing->id)->update($data);
            return;
        }

        DB::table('device_registrations')->insert($data + [
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'device_id' => $deviceId,
            'created_at' => now(),
        ]);
    }

    private function seedDemoRuntimeData(array $users, array $profiles, array $setup): void
    {
        $demoUsers = collect($users)
            ->filter(fn ($user, $key) => $key !== 'admin' && !empty($user?->id))
            ->map(fn ($user) => (int) $user->id)
            ->values()
            ->all();

        if (empty($demoUsers)) {
            return;
        }

        DB::table('attendance_location_tracks')->whereIn('user_id', $demoUsers)->delete();
        DB::table('attendance_approvals')->whereIn('user_id', $demoUsers)->delete();
        DB::table('attendance_logs')->whereIn('user_id', $demoUsers)->delete();
        DB::table('attendance_sync_queue')->whereIn('user_id', $demoUsers)->delete();
        DB::table('leave_requests')->whereIn('user_id', $demoUsers)->delete();
        DB::table('employee_attendance')->whereIn('user_id', $demoUsers)->delete();

        $today = Carbon::today('Asia/Kolkata');
        $hrApproverId = $users['hr']->id ?? null;

        $rahulAttendanceId = $this->insertAttendanceRecord([
            'user_id' => $users['employee']->id,
            'employee_profile_id' => $profiles['employee'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['general'],
            'attendance_policy_id' => $setup['policies']['office'],
            'attendance_date' => $today->toDateString(),
            'attendance_mode' => 'online',
            'work_mode' => 'office',
            'check_in_time' => $today->copy()->setTime(10, 14),
            'check_in_latitude' => 22.5728,
            'check_in_longitude' => 88.3641,
            'device_id' => 'seeded-employee-device-001',
            'request_ip' => '192.168.1.24',
            'late_minutes' => 4,
            'status' => 'late',
            'sync_status' => 'synced',
            'approval_status' => 'approved',
            'network_type' => 'wifi',
            'wifi_ip_match' => '192.168.1.0/24',
            'within_geofence' => 1,
            'within_wifi_ip' => 1,
            'approved_by' => $hrApproverId,
            'approved_at' => now(),
            'remarks' => 'Still on active session for employee dashboard testing.',
        ]);
        $this->insertAttendanceLog([
            'employee_attendance_id' => $rahulAttendanceId,
            'user_id' => $users['employee']->id,
            'employee_profile_id' => $profiles['employee'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['general'],
            'attendance_policy_id' => $setup['policies']['office'],
            'punch_type' => 'check_in',
            'punch_time' => $today->copy()->setTime(10, 14),
            'server_received_at' => $today->copy()->setTime(10, 14, 40),
            'attendance_mode' => 'online',
            'work_mode' => 'office',
            'internet_status' => 'online',
            'latitude' => 22.5728,
            'longitude' => 88.3641,
            'gps_accuracy_meters' => 16,
            'location_text' => 'Kolkata Office Gate 1',
            'device_id' => 'seeded-employee-device-001',
            'device_name' => 'Android Seed Device',
            'device_platform' => 'android',
            'app_version' => '1.0.0',
            'battery_level' => 78,
            'network_type' => 'wifi',
            'request_ip' => '192.168.1.24',
            'sync_status' => 'synced',
            'synced_at' => now(),
            'within_geofence' => 1,
            'within_wifi_ip' => 1,
            'is_exception' => 0,
        ]);
        $this->insertLocationTrack([
            'employee_attendance_id' => $rahulAttendanceId,
            'user_id' => $users['employee']->id,
            'recorded_at' => $today->copy()->setTime(11, 5),
            'latitude' => 22.5729,
            'longitude' => 88.3640,
            'gps_accuracy_meters' => 12,
            'battery_level' => 74,
            'network_type' => 'wifi',
            'is_background' => 1,
            'speed_kmph' => 0,
            'source' => 'seed_runtime',
            'sync_status' => 'synced',
        ]);
        $this->insertLocationTrack([
            'employee_attendance_id' => $rahulAttendanceId,
            'user_id' => $users['employee']->id,
            'recorded_at' => $today->copy()->setTime(12, 20),
            'latitude' => 22.5731,
            'longitude' => 88.3642,
            'gps_accuracy_meters' => 10,
            'battery_level' => 69,
            'network_type' => 'wifi',
            'is_background' => 1,
            'speed_kmph' => 0,
            'source' => 'seed_runtime',
            'sync_status' => 'synced',
        ]);

        $priyaAttendanceId = $this->insertAttendanceRecord([
            'user_id' => $users['priya']->id,
            'employee_profile_id' => $profiles['priya'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['general'],
            'attendance_policy_id' => $setup['policies']['office'],
            'attendance_date' => $today->toDateString(),
            'attendance_mode' => 'online',
            'work_mode' => 'office',
            'check_in_time' => $today->copy()->setTime(9, 57),
            'check_out_time' => $today->copy()->setTime(18, 11),
            'check_in_latitude' => 22.5727,
            'check_in_longitude' => 88.3638,
            'check_out_latitude' => 22.5726,
            'check_out_longitude' => 88.3639,
            'device_id' => 'seeded-employee-device-002',
            'request_ip' => '192.168.1.32',
            'total_working_minutes' => 494,
            'late_minutes' => 0,
            'overtime_minutes' => 14,
            'status' => 'present',
            'sync_status' => 'synced',
            'approval_status' => 'approved',
            'network_type' => 'wifi',
            'wifi_ip_match' => '192.168.1.0/24',
            'within_geofence' => 1,
            'within_wifi_ip' => 1,
            'approved_by' => $hrApproverId,
            'approved_at' => now(),
        ]);
        $this->insertAttendanceLog([
            'employee_attendance_id' => $priyaAttendanceId,
            'user_id' => $users['priya']->id,
            'employee_profile_id' => $profiles['priya'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['general'],
            'attendance_policy_id' => $setup['policies']['office'],
            'punch_type' => 'check_in',
            'punch_time' => $today->copy()->setTime(9, 57),
            'server_received_at' => $today->copy()->setTime(9, 57, 12),
            'attendance_mode' => 'online',
            'work_mode' => 'office',
            'internet_status' => 'online',
            'latitude' => 22.5727,
            'longitude' => 88.3638,
            'gps_accuracy_meters' => 18,
            'location_text' => 'Kolkata Office Reception',
            'device_id' => 'seeded-employee-device-002',
            'device_name' => 'iPhone 15',
            'device_platform' => 'ios',
            'app_version' => '1.0.0',
            'battery_level' => 89,
            'network_type' => 'wifi',
            'request_ip' => '192.168.1.32',
            'sync_status' => 'synced',
            'synced_at' => now(),
            'within_geofence' => 1,
            'within_wifi_ip' => 1,
            'is_exception' => 0,
        ]);
        $this->insertAttendanceLog([
            'employee_attendance_id' => $priyaAttendanceId,
            'user_id' => $users['priya']->id,
            'employee_profile_id' => $profiles['priya'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['general'],
            'attendance_policy_id' => $setup['policies']['office'],
            'punch_type' => 'check_out',
            'punch_time' => $today->copy()->setTime(18, 11),
            'server_received_at' => $today->copy()->setTime(18, 11, 8),
            'attendance_mode' => 'online',
            'work_mode' => 'office',
            'internet_status' => 'online',
            'latitude' => 22.5726,
            'longitude' => 88.3639,
            'gps_accuracy_meters' => 18,
            'location_text' => 'Kolkata Office Reception',
            'device_id' => 'seeded-employee-device-002',
            'device_name' => 'iPhone 15',
            'device_platform' => 'ios',
            'app_version' => '1.0.0',
            'battery_level' => 41,
            'network_type' => 'wifi',
            'request_ip' => '192.168.1.32',
            'sync_status' => 'synced',
            'synced_at' => now(),
            'within_geofence' => 1,
            'within_wifi_ip' => 1,
            'is_exception' => 0,
        ]);

        $amitAttendanceId = $this->insertAttendanceRecord([
            'user_id' => $users['amit']->id,
            'employee_profile_id' => $profiles['amit'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['morning'],
            'attendance_policy_id' => $setup['policies']['field'],
            'attendance_date' => $today->toDateString(),
            'attendance_mode' => 'online',
            'work_mode' => 'field',
            'check_in_time' => $today->copy()->setTime(8, 9),
            'check_in_latitude' => 22.5602,
            'check_in_longitude' => 88.4103,
            'device_id' => 'seeded-employee-device-003',
            'request_ip' => '49.36.121.22',
            'total_working_minutes' => 255,
            'status' => 'present',
            'sync_status' => 'synced',
            'approval_status' => 'approved',
            'network_type' => 'mobile',
            'within_geofence' => null,
            'within_wifi_ip' => null,
            'approved_by' => $hrApproverId,
            'approved_at' => now(),
            'remarks' => 'Active field route visit.',
        ]);
        $this->insertAttendanceLog([
            'employee_attendance_id' => $amitAttendanceId,
            'user_id' => $users['amit']->id,
            'employee_profile_id' => $profiles['amit'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['morning'],
            'attendance_policy_id' => $setup['policies']['field'],
            'punch_type' => 'check_in',
            'punch_time' => $today->copy()->setTime(8, 9),
            'server_received_at' => $today->copy()->setTime(8, 9, 20),
            'attendance_mode' => 'online',
            'work_mode' => 'field',
            'internet_status' => 'online',
            'latitude' => 22.5602,
            'longitude' => 88.4103,
            'gps_accuracy_meters' => 22,
            'location_text' => 'Salt Lake Client Zone',
            'device_id' => 'seeded-employee-device-003',
            'device_name' => 'Samsung Field Tab',
            'device_platform' => 'android',
            'app_version' => '1.0.0',
            'battery_level' => 67,
            'network_type' => 'mobile',
            'request_ip' => '49.36.121.22',
            'sync_status' => 'synced',
            'synced_at' => now(),
            'is_exception' => 0,
        ]);
        $this->insertLocationTrack([
            'employee_attendance_id' => $amitAttendanceId,
            'user_id' => $users['amit']->id,
            'recorded_at' => $today->copy()->setTime(10, 15),
            'latitude' => 22.5670,
            'longitude' => 88.4165,
            'gps_accuracy_meters' => 19,
            'battery_level' => 58,
            'network_type' => 'mobile',
            'is_background' => 1,
            'speed_kmph' => 12,
            'source' => 'seed_runtime',
            'sync_status' => 'synced',
        ]);

        $rituAttendanceId = $this->insertAttendanceRecord([
            'user_id' => $users['ritu']->id,
            'employee_profile_id' => $profiles['ritu'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['general'],
            'attendance_policy_id' => $setup['policies']['wfh'],
            'attendance_date' => $today->toDateString(),
            'attendance_mode' => 'online',
            'work_mode' => 'wfh',
            'check_in_time' => $today->copy()->setTime(10, 3),
            'check_out_time' => $today->copy()->setTime(18, 4),
            'device_id' => 'seeded-employee-device-004',
            'request_ip' => '103.87.44.21',
            'total_working_minutes' => 481,
            'status' => 'pending_approval',
            'sync_status' => 'synced',
            'approval_status' => 'pending_approval',
            'network_type' => 'wifi',
            'within_geofence' => null,
            'within_wifi_ip' => null,
            'remarks' => 'WFH release coordination and vendor calls.',
        ]);
        $rituLogId = $this->insertAttendanceLog([
            'employee_attendance_id' => $rituAttendanceId,
            'user_id' => $users['ritu']->id,
            'employee_profile_id' => $profiles['ritu'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['general'],
            'attendance_policy_id' => $setup['policies']['wfh'],
            'punch_type' => 'check_out',
            'punch_time' => $today->copy()->setTime(18, 4),
            'server_received_at' => $today->copy()->setTime(18, 4, 10),
            'attendance_mode' => 'online',
            'work_mode' => 'wfh',
            'internet_status' => 'online',
            'latitude' => 22.6010,
            'longitude' => 88.3920,
            'gps_accuracy_meters' => 80,
            'location_text' => 'Remote Home Network',
            'device_id' => 'seeded-employee-device-004',
            'device_name' => 'MacBook Air',
            'device_platform' => 'ios',
            'app_version' => '1.0.0',
            'battery_level' => 52,
            'network_type' => 'wifi',
            'request_ip' => '103.87.44.21',
            'sync_status' => 'synced',
            'synced_at' => now(),
            'is_exception' => 1,
            'exception_reason' => 'wfh_note_required',
        ]);
        $this->insertApproval([
            'user_id' => $users['ritu']->id,
            'employee_attendance_id' => $rituAttendanceId,
            'attendance_log_id' => $rituLogId,
            'requested_by' => $users['ritu']->id,
            'approval_type' => 'wfh_note_required',
            'status' => 'pending_approval',
            'requested_at' => now(),
            'reason' => 'WFH attendance requires HR review.',
        ]);

        $arjunAttendanceId = $this->insertAttendanceRecord([
            'user_id' => $users['arjun']->id,
            'employee_profile_id' => $profiles['arjun'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['general'],
            'attendance_policy_id' => $setup['policies']['office'],
            'attendance_date' => $today->toDateString(),
            'attendance_mode' => 'online',
            'work_mode' => 'office',
            'check_in_time' => $today->copy()->setTime(10, 26),
            'check_in_latitude' => 22.5899,
            'check_in_longitude' => 88.4011,
            'device_id' => 'seeded-employee-device-005',
            'request_ip' => '106.51.90.33',
            'late_minutes' => 16,
            'status' => 'pending_approval',
            'sync_status' => 'synced',
            'approval_status' => 'pending_approval',
            'network_type' => 'mobile',
            'within_geofence' => 0,
            'within_wifi_ip' => 0,
            'remarks' => 'Customer visit before office arrival.',
        ]);
        $arjunLogId = $this->insertAttendanceLog([
            'employee_attendance_id' => $arjunAttendanceId,
            'user_id' => $users['arjun']->id,
            'employee_profile_id' => $profiles['arjun'] ?? null,
            'branch_id' => $setup['branch_id'],
            'shift_id' => $setup['shifts']['general'],
            'attendance_policy_id' => $setup['policies']['office'],
            'punch_type' => 'check_in',
            'punch_time' => $today->copy()->setTime(10, 26),
            'server_received_at' => $today->copy()->setTime(10, 26, 20),
            'attendance_mode' => 'online',
            'work_mode' => 'office',
            'internet_status' => 'online',
            'latitude' => 22.5899,
            'longitude' => 88.4011,
            'gps_accuracy_meters' => 25,
            'location_text' => 'Bypass Sales Corridor',
            'device_id' => 'seeded-employee-device-005',
            'device_name' => 'Moto G',
            'device_platform' => 'android',
            'app_version' => '1.0.0',
            'battery_level' => 71,
            'network_type' => 'mobile',
            'request_ip' => '106.51.90.33',
            'sync_status' => 'synced',
            'synced_at' => now(),
            'within_geofence' => 0,
            'within_wifi_ip' => 0,
            'is_exception' => 1,
            'exception_reason' => 'outside_geofence,wifi_ip_not_allowed,mobile_data_not_allowed',
        ]);
        $this->insertApproval([
            'user_id' => $users['arjun']->id,
            'employee_attendance_id' => $arjunAttendanceId,
            'attendance_log_id' => $arjunLogId,
            'requested_by' => $users['arjun']->id,
            'approval_type' => 'outside_geofence',
            'status' => 'pending_approval',
            'requested_at' => now(),
            'reason' => 'Office policy violation needs HR review.',
        ]);

        if (!empty($users['sanjay']->id) && !empty($profiles['sanjay'])) {
            $this->insertLeaveRequest([
                'user_id' => $users['sanjay']->id,
                'employee_profile_id' => $profiles['sanjay'],
                'leave_type_id' => $setup['leave_types']['casual'],
                'from_date' => $today->copy()->addDay()->toDateString(),
                'to_date' => $today->copy()->addDay()->toDateString(),
                'total_days' => 1,
                'reason' => 'Family work',
                'status' => 'approved',
                'approved_by' => $hrApproverId,
                'applied_at' => now()->subDay(),
                'approved_at' => now()->subHours(12),
            ]);
        }

        foreach ([
            ['key' => 'employee', 'start' => [10, 2], 'end' => [18, 6], 'status' => 'present', 'late' => 0],
            ['key' => 'priya', 'start' => [10, 6], 'end' => [18, 2], 'status' => 'present', 'late' => 0],
            ['key' => 'amit', 'start' => [8, 13], 'end' => [16, 18], 'status' => 'present', 'late' => 3],
        ] as $daySeed) {
            for ($daysAgo = 1; $daysAgo <= 4; $daysAgo += 1) {
                $date = $today->copy()->subDays($daysAgo);
                if (in_array(strtolower($date->format('D')), ['sun'], true)) {
                    continue;
                }
                $user = $users[$daySeed['key']] ?? null;
                $profileId = $profiles[$daySeed['key']] ?? null;
                if (empty($user?->id) || empty($profileId)) {
                    continue;
                }
                $in = $date->copy()->setTime($daySeed['start'][0], $daySeed['start'][1] + $daysAgo);
                $out = $date->copy()->setTime($daySeed['end'][0], $daySeed['end'][1] + $daysAgo);
                $shiftId = $daySeed['key'] === 'amit' ? $setup['shifts']['morning'] : $setup['shifts']['general'];
                $policyId = $daySeed['key'] === 'amit' ? $setup['policies']['field'] : $setup['policies']['office'];
                $workMode = $daySeed['key'] === 'amit' ? 'field' : 'office';
                $lat = $daySeed['key'] === 'amit' ? 22.5651 : 22.5726;
                $lng = $daySeed['key'] === 'amit' ? 88.4122 : 88.3639;

                $attendanceId = $this->insertAttendanceRecord([
                    'user_id' => $user->id,
                    'employee_profile_id' => $profileId,
                    'branch_id' => $setup['branch_id'],
                    'shift_id' => $shiftId,
                    'attendance_policy_id' => $policyId,
                    'attendance_date' => $date->toDateString(),
                    'attendance_mode' => 'online',
                    'work_mode' => $workMode,
                    'check_in_time' => $in,
                    'check_out_time' => $out,
                    'check_in_latitude' => $lat,
                    'check_in_longitude' => $lng,
                    'check_out_latitude' => $lat,
                    'check_out_longitude' => $lng,
                    'request_ip' => $daySeed['key'] === 'amit' ? '49.36.121.22' : '192.168.1.30',
                    'total_working_minutes' => max(0, $in->diffInMinutes($out) - 60),
                    'late_minutes' => $daySeed['late'] ? $daySeed['late'] + $daysAgo : 0,
                    'status' => $daySeed['late'] ? 'late' : $daySeed['status'],
                    'sync_status' => 'synced',
                    'approval_status' => 'approved',
                    'network_type' => $daySeed['key'] === 'amit' ? 'mobile' : 'wifi',
                    'wifi_ip_match' => $daySeed['key'] === 'amit' ? null : '192.168.1.0/24',
                    'within_geofence' => $daySeed['key'] === 'amit' ? null : 1,
                    'within_wifi_ip' => $daySeed['key'] === 'amit' ? null : 1,
                    'approved_by' => $hrApproverId,
                    'approved_at' => now(),
                ]);

                $this->insertAttendanceLog([
                    'employee_attendance_id' => $attendanceId,
                    'user_id' => $user->id,
                    'employee_profile_id' => $profileId,
                    'branch_id' => $setup['branch_id'],
                    'shift_id' => $shiftId,
                    'attendance_policy_id' => $policyId,
                    'punch_type' => 'check_out',
                    'punch_time' => $out,
                    'server_received_at' => $out->copy()->addSeconds(35),
                    'attendance_mode' => 'online',
                    'work_mode' => $workMode,
                    'internet_status' => 'online',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'gps_accuracy_meters' => 20,
                    'location_text' => $daySeed['key'] === 'amit' ? 'Field Route Sync' : 'Kolkata Office',
                    'device_id' => 'seeded-history-log',
                    'device_name' => 'Seed Device',
                    'device_platform' => 'android',
                    'app_version' => '1.0.0',
                    'battery_level' => 60,
                    'network_type' => $daySeed['key'] === 'amit' ? 'mobile' : 'wifi',
                    'request_ip' => $daySeed['key'] === 'amit' ? '49.36.121.22' : '192.168.1.30',
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                    'within_geofence' => $daySeed['key'] === 'amit' ? null : 1,
                    'within_wifi_ip' => $daySeed['key'] === 'amit' ? null : 1,
                    'is_exception' => 0,
                ]);
            }
        }
    }

    private function insertAttendanceRecord(array $payload): int
    {
        return (int) DB::table('employee_attendance')->insertGetId($payload + [
            'uuid' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertAttendanceLog(array $payload): int
    {
        return (int) DB::table('attendance_logs')->insertGetId($payload + [
            'uuid' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertLocationTrack(array $payload): int
    {
        return (int) DB::table('attendance_location_tracks')->insertGetId($payload + [
            'uuid' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertApproval(array $payload): int
    {
        return (int) DB::table('attendance_approvals')->insertGetId($payload + [
            'uuid' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertLeaveRequest(array $payload): int
    {
        return (int) DB::table('leave_requests')->insertGetId($payload + [
            'uuid' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function upsertMenu(
        string $name,
        ?string $href,
        ?string $description,
        string $icon,
        bool $isHeader,
        int $position,
        ?int $parentId = null
    ): array {
        $existing = DB::table('dashboard_menu')->where('name', $name)->first();

        $payload = [
            'parent_id' => $parentId,
            'position' => $position,
            'icon_class' => $icon,
            'href' => $href,
            'description' => $description,
            'is_dropdown_head' => $isHeader ? 1 : 0,
            'status' => 'Active',
            'deleted_at' => null,
            'updated_at' => now(),
            'updated_at_ip' => '127.0.0.1',
        ];

        if ($existing) {
            DB::table('dashboard_menu')->where('id', $existing->id)->update($payload + [
                'uuid' => $existing->uuid ?: (string) Str::uuid(),
            ]);
            return (array) DB::table('dashboard_menu')->where('id', $existing->id)->first();
        }

        $id = DB::table('dashboard_menu')->insertGetId($payload + [
            'uuid' => (string) Str::uuid(),
            'name' => $name,
            'created_at' => now(),
            'created_at_ip' => '127.0.0.1',
        ]);

        return (array) DB::table('dashboard_menu')->where('id', $id)->first();
    }

    private function upsertPrivilege(int $menuId, string $action, string $description): array
    {
        $existing = DB::table('page_privilege')
            ->where('dashboard_menu_id', $menuId)
            ->where('action', $action)
            ->first();

        $payload = [
            'key' => strtolower(trim($menuId . '.' . $action)),
            'description' => $description,
            'status' => 'Active',
            'deleted_at' => null,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('page_privilege')->where('id', $existing->id)->update($payload + [
                'uuid' => $existing->uuid ?: (string) Str::uuid(),
            ]);
            return (array) DB::table('page_privilege')->where('id', $existing->id)->first();
        }

        $id = DB::table('page_privilege')->insertGetId($payload + [
            'uuid' => (string) Str::uuid(),
            'dashboard_menu_id' => $menuId,
            'action' => $action,
            'created_at' => now(),
            'created_at_ip' => '127.0.0.1',
        ]);

        return (array) DB::table('page_privilege')->where('id', $id)->first();
    }
}
