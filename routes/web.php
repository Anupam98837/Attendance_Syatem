<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.auth.login')->name('login');
Route::redirect('/login', '/');
Route::redirect('/register', '/');
Route::redirect('/forgot-password', '/');
Route::redirect('/reset-password', '/');

Route::view('/dashboard', 'modules.attendance.employee.employeeDashboard');
Route::view('/profile', 'pages.pages.user.profile');
Route::redirect('/user', '/users/manage');

Route::view('/users/manage', 'pages.pages.users.manageUsers');
Route::view('/user/manage', 'pages.pages.users.manageUsers');

Route::view('/dashboard-menu/manage', 'modules.dashboardMenu.manageDashboardMenu');
Route::view('/dashboard-menu/create', 'modules.dashboardMenu.createDashboardMenu');

Route::view('/page-privilege/manage', 'modules.privileges.managePagePrivileges');
Route::view('/page-privilege/create', 'modules.privileges.createPagePrivileges');

Route::view('/user-privileges/manage', 'modules.privileges.assignPrivileges');
Route::view('/role-privileges/manage', 'modules.privileges.assignRolePrivileges');

Route::view('/attendance/company', 'modules.attendance.setup.manageCompanySettings');
Route::view('/attendance/branches', 'modules.attendance.setup.manageBranches');
Route::view('/attendance/branch-networks', 'modules.attendance.setup.manageBranchNetworks');
Route::view('/attendance/departments', 'modules.attendance.setup.manageDepartments');
Route::view('/attendance/designations', 'modules.attendance.setup.manageDesignations');
Route::view('/attendance/shifts', 'modules.attendance.setup.manageShifts');
Route::view('/attendance/policies', 'modules.attendance.setup.managePolicies');
Route::view('/attendance/holidays', 'modules.attendance.setup.manageHolidays');
Route::view('/attendance/leave-types', 'modules.attendance.setup.manageLeaveTypes');
Route::view('/attendance/employees', 'modules.attendance.workforce.manageEmployees');
Route::view('/attendance/records', 'modules.attendance.operations.manageAttendanceRegister');
Route::view('/attendance/today', 'modules.attendance.operations.manageTodayAttendance');
Route::view('/attendance/monthly', 'modules.attendance.operations.manageMonthlyAttendance');
Route::view('/attendance/pending-approvals', 'modules.attendance.operations.managePendingApprovals');
Route::view('/attendance/reports', 'modules.attendance.operations.manageReports');
Route::view('/attendance/offline-sync-logs', 'modules.attendance.operations.manageOfflineSyncLogs');
Route::view('/attendance/location-exceptions', 'modules.attendance.operations.manageLocationExceptions');
Route::view('/attendance/leaves', 'modules.attendance.operations.manageLeaves');
Route::view('/attendance/employee-mobile', 'modules.attendance.employee.manageEmployeeAppBlueprint');
Route::redirect('/attendance/employee-dashboard', '/dashboard');
Route::view('/attendance/employee-history',   'modules.attendance.employee.employeeAttendanceHistory');
Route::view('/attendance/employee-leaves',    'modules.attendance.employee.employeeLeaves');
Route::view('/attendance/employee-activity',  'modules.attendance.employee.employeeActivityLog');
Route::view('/attendance/activity-logs',      'modules.attendance.operations.manageActivityLogs');

Route::get('/notifications', function () {
    return view('pages.pages.common.placeholder', [
        'pageTitle' => 'Notifications',
        'pageLead' => 'Notification delivery is active in the shared admin shell. A full notifications page can be added on top of this starter.',
        'pageIcon' => 'fa-solid fa-bell',
    ]);
});
