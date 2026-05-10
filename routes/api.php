<?php

use App\Http\Controllers\API\AttendanceEmployeeController;
use App\Http\Controllers\API\AttendanceMobileController;
use App\Http\Controllers\API\AttendanceOperationsController;
use App\Http\Controllers\API\AttendanceSetupController;
use App\Http\Controllers\API\DashboardMenuController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PagePrivilegeController;
use App\Http\Controllers\API\RolePrivilegeController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ActivityTrackingController;
use App\Http\Controllers\API\UserPrivilegeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);

    Route::get('/check', [UserController::class, 'authenticateToken']);

    Route::middleware('checkAuth')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/me-role', [UserController::class, 'getMyRole']);
        Route::get('/profile', [UserController::class, 'getProfile']);
    });
});

Route::get('/my/sidebar-menus', [UserPrivilegeController::class, 'mySidebarMenus']);

Route::middleware('checkAuth')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/all', [UserController::class, 'all']);
    Route::get('/{id}', [UserController::class, 'show']);

    Route::post('/', [UserController::class, 'store']);
    Route::match(['put', 'patch'], '/{id}', [UserController::class, 'update']);

    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::post('/{id}/restore', [UserController::class, 'restore']);
    Route::delete('/{id}/force', [UserController::class, 'forceDelete']);

    Route::patch('/{id}/password', [UserController::class, 'updatePassword']);
    Route::post('/{id}/image', [UserController::class, 'updateImage']);
    Route::post('/{uuid}/cv', [UserController::class, 'uploadCvByUuid']);
    Route::post('/import-csv', [UserController::class, 'importUsersCsv']);
});

Route::middleware('checkAuth')->group(function () {
    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::post('/profile', [UserController::class, 'updateMyProfile']);
    Route::patch('/profile/password', [UserController::class, 'updateMyPassword']);

    Route::get('/user/{idOrUuid}', [UserPrivilegeController::class, 'show']);

    Route::prefix('dashboard-menus')->group(function () {
        Route::get('/', [DashboardMenuController::class, 'index']);
        Route::get('/tree', [DashboardMenuController::class, 'tree']);
        Route::get('/all-with-privileges', [DashboardMenuController::class, 'allWithPrivileges']);
        Route::get('/archived', [DashboardMenuController::class, 'archived']);
        Route::get('/bin', [DashboardMenuController::class, 'bin']);
        Route::post('/', [DashboardMenuController::class, 'store']);
        Route::post('/reorder', [DashboardMenuController::class, 'reorder']);
        Route::get('/{identifier}', [DashboardMenuController::class, 'show']);
        Route::match(['put', 'patch'], '/{identifier}', [DashboardMenuController::class, 'update']);
        Route::post('/{identifier}/archive', [DashboardMenuController::class, 'archive']);
        Route::post('/{identifier}/unarchive', [DashboardMenuController::class, 'unarchive']);
        Route::delete('/{identifier}', [DashboardMenuController::class, 'destroy']);
        Route::post('/{identifier}/restore', [DashboardMenuController::class, 'restore']);
        Route::delete('/{identifier}/force', [DashboardMenuController::class, 'forceDelete']);
    });

    Route::prefix('privileges')->group(function () {
        Route::get('/', [PagePrivilegeController::class, 'index']);
        Route::get('/index-of-api', [PagePrivilegeController::class, 'indexOfApi']);
        Route::get('/archived', [PagePrivilegeController::class, 'archived']);
        Route::get('/bin', [PagePrivilegeController::class, 'bin']);
        Route::post('/', [PagePrivilegeController::class, 'store']);
        Route::post('/reorder', [PagePrivilegeController::class, 'reorder']);
        Route::get('/{identifier}', [PagePrivilegeController::class, 'show']);
        Route::match(['put', 'patch'], '/{identifier}', [PagePrivilegeController::class, 'update']);
        Route::post('/{identifier}/archive', [PagePrivilegeController::class, 'archive']);
        Route::post('/{identifier}/unarchive', [PagePrivilegeController::class, 'unarchive']);
        Route::delete('/{identifier}', [PagePrivilegeController::class, 'destroy']);
        Route::post('/{identifier}/restore', [PagePrivilegeController::class, 'restore']);
        Route::delete('/{identifier}/force', [PagePrivilegeController::class, 'forceDelete']);
    });

    Route::prefix('user-privileges')->group(function () {
        Route::get('/list', [UserPrivilegeController::class, 'list']);
        Route::post('/sync', [UserPrivilegeController::class, 'sync']);
        Route::post('/assign', [UserPrivilegeController::class, 'assign']);
        Route::post('/unassign', [UserPrivilegeController::class, 'unassign']);
        Route::delete('/', [UserPrivilegeController::class, 'destroy']);
        Route::get('/my-modules', [UserPrivilegeController::class, 'myModules']);
        Route::get('/modules', [UserPrivilegeController::class, 'modulesForUser']);
        Route::get('/modules/{idOrUuid}', [UserPrivilegeController::class, 'modulesForUserByPath']);
        Route::get('/user/by-uuid', [UserPrivilegeController::class, 'byUuid']);
    });

    Route::prefix('role-privileges')->group(function () {
        Route::get('/list', [RolePrivilegeController::class, 'list']);
        Route::post('/sync', [RolePrivilegeController::class, 'sync']);
        Route::post('/assign', [RolePrivilegeController::class, 'assign']);
        Route::post('/unassign', [RolePrivilegeController::class, 'unassign']);
        Route::delete('/', [RolePrivilegeController::class, 'destroy']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::get('/drawer', [NotificationController::class, 'drawer']);
        Route::post('/read-all', [NotificationController::class, 'markAllRead']);
        Route::post('/{id}/read', [NotificationController::class, 'markRead']);
    });

    Route::get('/role/sidebar-menus', [RolePrivilegeController::class, 'sidebarMenusForRole']);
});

Route::prefix('attendance')->middleware('checkAuth')->group(function () {
    Route::middleware('role:admin,hr')->prefix('admin')->group(function () {
        Route::get('/company', [AttendanceSetupController::class, 'showCompany']);
        Route::patch('/company', [AttendanceSetupController::class, 'updateCompany']);
        Route::get('/request-ip', [AttendanceSetupController::class, 'requestIp']);

        Route::get('/branches/{branchId}/networks', [AttendanceSetupController::class, 'indexBranchNetworks']);
        Route::post('/branches/{branchId}/networks', [AttendanceSetupController::class, 'storeBranchNetwork']);
        Route::match(['put', 'patch'], '/branches/{branchId}/networks/{networkId}', [AttendanceSetupController::class, 'updateBranchNetwork']);
        Route::delete('/branches/{branchId}/networks/{networkId}', [AttendanceSetupController::class, 'destroyBranchNetwork']);

        Route::get('/branches/{id}', [AttendanceSetupController::class, 'show'])
            ->defaults('resource', 'branches');
        Route::match(['put', 'patch'], '/branches/{id}', [AttendanceSetupController::class, 'update'])
            ->defaults('resource', 'branches');
        Route::delete('/branches/{id}', [AttendanceSetupController::class, 'destroy'])
            ->defaults('resource', 'branches');

        Route::get('/departments/{id}', [AttendanceSetupController::class, 'show'])
            ->defaults('resource', 'departments');
        Route::match(['put', 'patch'], '/departments/{id}', [AttendanceSetupController::class, 'update'])
            ->defaults('resource', 'departments');
        Route::delete('/departments/{id}', [AttendanceSetupController::class, 'destroy'])
            ->defaults('resource', 'departments');

        Route::get('/designations/{id}', [AttendanceSetupController::class, 'show'])
            ->defaults('resource', 'designations');
        Route::match(['put', 'patch'], '/designations/{id}', [AttendanceSetupController::class, 'update'])
            ->defaults('resource', 'designations');
        Route::delete('/designations/{id}', [AttendanceSetupController::class, 'destroy'])
            ->defaults('resource', 'designations');

        Route::get('/shifts/{id}', [AttendanceSetupController::class, 'show'])
            ->defaults('resource', 'shifts');
        Route::match(['put', 'patch'], '/shifts/{id}', [AttendanceSetupController::class, 'update'])
            ->defaults('resource', 'shifts');
        Route::delete('/shifts/{id}', [AttendanceSetupController::class, 'destroy'])
            ->defaults('resource', 'shifts');

        Route::get('/attendance-policies/{id}', [AttendanceSetupController::class, 'show'])
            ->defaults('resource', 'attendance-policies');
        Route::match(['put', 'patch'], '/attendance-policies/{id}', [AttendanceSetupController::class, 'update'])
            ->defaults('resource', 'attendance-policies');
        Route::delete('/attendance-policies/{id}', [AttendanceSetupController::class, 'destroy'])
            ->defaults('resource', 'attendance-policies');

        Route::get('/holidays/{id}', [AttendanceSetupController::class, 'show'])
            ->defaults('resource', 'holidays');
        Route::match(['put', 'patch'], '/holidays/{id}', [AttendanceSetupController::class, 'update'])
            ->defaults('resource', 'holidays');
        Route::delete('/holidays/{id}', [AttendanceSetupController::class, 'destroy'])
            ->defaults('resource', 'holidays');

        Route::get('/leave-types/{id}', [AttendanceSetupController::class, 'show'])
            ->defaults('resource', 'leave-types');
        Route::match(['put', 'patch'], '/leave-types/{id}', [AttendanceSetupController::class, 'update'])
            ->defaults('resource', 'leave-types');
        Route::delete('/leave-types/{id}', [AttendanceSetupController::class, 'destroy'])
            ->defaults('resource', 'leave-types');

        Route::get('/{resource}', [AttendanceSetupController::class, 'index'])
            ->where('resource', 'departments|designations|branches|shifts|attendance-policies|holidays|leave-types');
        Route::post('/{resource}', [AttendanceSetupController::class, 'store'])
            ->where('resource', 'departments|designations|branches|shifts|attendance-policies|holidays|leave-types');
        Route::get('/{resource}/{id}', [AttendanceSetupController::class, 'show'])
            ->where('resource', 'departments|designations|branches|shifts|attendance-policies|holidays|leave-types');
        Route::match(['put', 'patch'], '/{resource}/{id}', [AttendanceSetupController::class, 'update'])
            ->where('resource', 'departments|designations|branches|shifts|attendance-policies|holidays|leave-types');
        Route::delete('/{resource}/{id}', [AttendanceSetupController::class, 'destroy'])
            ->where('resource', 'departments|designations|branches|shifts|attendance-policies|holidays|leave-types');
    });

    Route::middleware('role:admin,hr')->prefix('hr')->group(function () {
        Route::get('/employees', [AttendanceEmployeeController::class, 'index']);
        Route::post('/employees', [AttendanceEmployeeController::class, 'store']);
        Route::get('/employees/{id}', [AttendanceEmployeeController::class, 'show']);
        Route::match(['put', 'patch'], '/employees/{id}', [AttendanceEmployeeController::class, 'update']);
        Route::post('/employees/{id}/devices', [AttendanceEmployeeController::class, 'registerDevice']);
        Route::get('/employees/{id}/attendance-history', [AttendanceEmployeeController::class, 'attendanceHistory']);

        Route::get('/dashboard', [AttendanceOperationsController::class, 'dashboard']);
        Route::get('/live-attendance', [AttendanceOperationsController::class, 'liveAttendance']);
        Route::get('/attendance', [AttendanceOperationsController::class, 'attendanceIndex']);
        Route::get('/attendance/{attendanceId}/detail', [AttendanceOperationsController::class, 'attendanceDetail']);
        Route::get('/pending-approvals', [AttendanceOperationsController::class, 'pendingApprovals']);
        Route::post('/approvals/{id}/decision', [AttendanceOperationsController::class, 'decideApproval']);
        Route::post('/attendance/{attendanceId}/manual-correction', [AttendanceOperationsController::class, 'manualCorrection']);
        Route::get('/reports', [AttendanceOperationsController::class, 'reports']);
        Route::get('/offline-sync-logs', [AttendanceOperationsController::class, 'offlineSyncLogs']);
        Route::get('/location-exceptions', [AttendanceOperationsController::class, 'locationExceptions']);
        Route::get('/leaves', [AttendanceOperationsController::class, 'leaveIndex']);
        Route::post('/leaves/{leaveId}/decision', [AttendanceOperationsController::class, 'decideLeave']);
        // Activity tracking viewer
        Route::get('/activity-logs', [ActivityTrackingController::class, 'listActivityLogs']);
        Route::get('/activity-logs/{id}', [ActivityTrackingController::class, 'getActivityLog']);
    });

    Route::middleware('role:employee')->prefix('mobile')->group(function () {
        Route::get('/bootstrap', [AttendanceMobileController::class, 'bootstrap']);
        Route::get('/request-ip', [AttendanceMobileController::class, 'requestIp']);
        Route::get('/active-session', [AttendanceMobileController::class, 'activeSession']);
        Route::get('/history', [AttendanceMobileController::class, 'history']);
        Route::post('/punch', [AttendanceMobileController::class, 'punch']);
        Route::post('/sync', [AttendanceMobileController::class, 'sync']);
        Route::post('/activity-log/sync', [AttendanceMobileController::class, 'syncActivityLogs']);
        Route::get('/activity-log', [AttendanceMobileController::class, 'myActivityLogs']);
        Route::post('/location-ping', [AttendanceMobileController::class, 'locationPing']);
        Route::get('/sync-queue', [AttendanceMobileController::class, 'syncQueue']);
        Route::get('/leaves', [AttendanceMobileController::class, 'myLeaves']);
        Route::post('/leaves', [AttendanceMobileController::class, 'applyLeave']);
        Route::get('/summary', [AttendanceMobileController::class, 'summary']);
        // Activity tracking
        Route::post('/activity-log', [ActivityTrackingController::class, 'logActivity']);
        Route::get('/activity-log', [ActivityTrackingController::class, 'myActivityLog']);
    });

});
