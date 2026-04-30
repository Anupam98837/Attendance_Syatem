<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\DashboardMenuController;
use App\Http\Controllers\API\ClinicController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\DoctorProfileController;
use App\Http\Controllers\API\HospitalController;
use App\Http\Controllers\API\PagePrivilegeController;
use App\Http\Controllers\API\ReferenceMasterController;
use App\Http\Controllers\API\RolePrivilegeController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserPrivilegeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    // public
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'patientRegister']);
    Route::post('/patient-register', [UserController::class, 'patientRegister']);

    // optional backward compatibility
    Route::post('/student-register', [UserController::class, 'studentRegister']);

    Route::get('/check', [UserController::class, 'authenticateToken']);

    // protected
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

    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index']);
        Route::get('/all', [DepartmentController::class, 'all']);
        Route::get('/archived', [DepartmentController::class, 'archived']);
        Route::get('/bin', [DepartmentController::class, 'bin']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::get('/{identifier}', [DepartmentController::class, 'show']);
        Route::match(['put', 'patch'], '/{identifier}', [DepartmentController::class, 'update']);
        Route::delete('/{identifier}', [DepartmentController::class, 'destroy']);
        Route::post('/{identifier}/restore', [DepartmentController::class, 'restore']);
        Route::delete('/{identifier}/force', [DepartmentController::class, 'forceDelete']);
    });

    Route::prefix('hospitals')->group(function () {
        Route::get('/', [HospitalController::class, 'index']);
        Route::get('/all', [HospitalController::class, 'all']);
        Route::get('/bin', [HospitalController::class, 'bin']);
        Route::post('/', [HospitalController::class, 'store']);
        Route::get('/{identifier}', [HospitalController::class, 'show']);
        Route::match(['put', 'patch'], '/{identifier}', [HospitalController::class, 'update']);
        Route::delete('/{identifier}', [HospitalController::class, 'destroy']);
        Route::post('/{identifier}/restore', [HospitalController::class, 'restore']);
        Route::delete('/{identifier}/force', [HospitalController::class, 'forceDelete']);
    });

    Route::prefix('clinics')->group(function () {
        Route::get('/', [ClinicController::class, 'index']);
        Route::get('/all', [ClinicController::class, 'all']);
        Route::get('/bin', [ClinicController::class, 'bin']);
        Route::post('/', [ClinicController::class, 'store']);
        Route::get('/{identifier}', [ClinicController::class, 'show']);
        Route::match(['put', 'patch'], '/{identifier}', [ClinicController::class, 'update']);
        Route::delete('/{identifier}', [ClinicController::class, 'destroy']);
        Route::post('/{identifier}/restore', [ClinicController::class, 'restore']);
        Route::delete('/{identifier}/force', [ClinicController::class, 'forceDelete']);
    });

    Route::prefix('doctors')->group(function () {
        Route::get('/profile/{userUuid}', [DoctorProfileController::class, 'show']);
        Route::post('/profile/{userUuid}', [DoctorProfileController::class, 'save']);
    });

    Route::prefix('bookings')->group(function () {
        Route::get('/doctors/{doctorSlug}/bootstrap', [BookingController::class, 'bootstrap']);
        Route::post('/doctors/{doctorSlug}', [BookingController::class, 'store']);
        Route::get('/dashboard', [BookingController::class, 'dashboard']);
        Route::get('/mine', [BookingController::class, 'myBookings']);
        Route::post('/mine/{appointmentId}/cancel', [BookingController::class, 'cancelMyBooking']);
        Route::post('/mine/{appointmentId}/review', [BookingController::class, 'submitReview']);
        Route::get('/manage', [BookingController::class, 'adminIndex']);
        Route::post('/manage/{appointmentId}/status', [BookingController::class, 'adminUpdateStatus']);
    });

    Route::prefix('specializations')->group(function () {
        Route::get('/', [ReferenceMasterController::class, 'index'])->defaults('master', 'specializations');
        Route::get('/all', [ReferenceMasterController::class, 'all'])->defaults('master', 'specializations');
        Route::get('/bin', [ReferenceMasterController::class, 'bin'])->defaults('master', 'specializations');
        Route::post('/', [ReferenceMasterController::class, 'store'])->defaults('master', 'specializations');
        Route::get('/{identifier}', [ReferenceMasterController::class, 'show'])->defaults('master', 'specializations');
        Route::match(['put', 'patch'], '/{identifier}', [ReferenceMasterController::class, 'update'])->defaults('master', 'specializations');
        Route::delete('/{identifier}', [ReferenceMasterController::class, 'destroy'])->defaults('master', 'specializations');
        Route::post('/{identifier}/restore', [ReferenceMasterController::class, 'restore'])->defaults('master', 'specializations');
        Route::delete('/{identifier}/force', [ReferenceMasterController::class, 'forceDelete'])->defaults('master', 'specializations');
    });

    Route::prefix('designations')->group(function () {
        Route::get('/', [ReferenceMasterController::class, 'index'])->defaults('master', 'designations');
        Route::get('/all', [ReferenceMasterController::class, 'all'])->defaults('master', 'designations');
        Route::get('/bin', [ReferenceMasterController::class, 'bin'])->defaults('master', 'designations');
        Route::post('/', [ReferenceMasterController::class, 'store'])->defaults('master', 'designations');
        Route::get('/{identifier}', [ReferenceMasterController::class, 'show'])->defaults('master', 'designations');
        Route::match(['put', 'patch'], '/{identifier}', [ReferenceMasterController::class, 'update'])->defaults('master', 'designations');
        Route::delete('/{identifier}', [ReferenceMasterController::class, 'destroy'])->defaults('master', 'designations');
        Route::post('/{identifier}/restore', [ReferenceMasterController::class, 'restore'])->defaults('master', 'designations');
        Route::delete('/{identifier}/force', [ReferenceMasterController::class, 'forceDelete'])->defaults('master', 'designations');
    });

    Route::prefix('registration-councils')->group(function () {
        Route::get('/', [ReferenceMasterController::class, 'index'])->defaults('master', 'registration_councils');
        Route::get('/all', [ReferenceMasterController::class, 'all'])->defaults('master', 'registration_councils');
        Route::get('/bin', [ReferenceMasterController::class, 'bin'])->defaults('master', 'registration_councils');
        Route::post('/', [ReferenceMasterController::class, 'store'])->defaults('master', 'registration_councils');
        Route::get('/{identifier}', [ReferenceMasterController::class, 'show'])->defaults('master', 'registration_councils');
        Route::match(['put', 'patch'], '/{identifier}', [ReferenceMasterController::class, 'update'])->defaults('master', 'registration_councils');
        Route::delete('/{identifier}', [ReferenceMasterController::class, 'destroy'])->defaults('master', 'registration_councils');
        Route::post('/{identifier}/restore', [ReferenceMasterController::class, 'restore'])->defaults('master', 'registration_councils');
        Route::delete('/{identifier}/force', [ReferenceMasterController::class, 'forceDelete'])->defaults('master', 'registration_councils');
    });

    Route::prefix('languages')->group(function () {
        Route::get('/', [ReferenceMasterController::class, 'index'])->defaults('master', 'languages');
        Route::get('/all', [ReferenceMasterController::class, 'all'])->defaults('master', 'languages');
        Route::get('/bin', [ReferenceMasterController::class, 'bin'])->defaults('master', 'languages');
        Route::post('/', [ReferenceMasterController::class, 'store'])->defaults('master', 'languages');
        Route::get('/{identifier}', [ReferenceMasterController::class, 'show'])->defaults('master', 'languages');
        Route::match(['put', 'patch'], '/{identifier}', [ReferenceMasterController::class, 'update'])->defaults('master', 'languages');
        Route::delete('/{identifier}', [ReferenceMasterController::class, 'destroy'])->defaults('master', 'languages');
        Route::post('/{identifier}/restore', [ReferenceMasterController::class, 'restore'])->defaults('master', 'languages');
        Route::delete('/{identifier}/force', [ReferenceMasterController::class, 'forceDelete'])->defaults('master', 'languages');
    });

    Route::prefix('services')->group(function () {
        Route::get('/', [ReferenceMasterController::class, 'index'])->defaults('master', 'services');
        Route::get('/all', [ReferenceMasterController::class, 'all'])->defaults('master', 'services');
        Route::get('/bin', [ReferenceMasterController::class, 'bin'])->defaults('master', 'services');
        Route::post('/', [ReferenceMasterController::class, 'store'])->defaults('master', 'services');
        Route::get('/{identifier}', [ReferenceMasterController::class, 'show'])->defaults('master', 'services');
        Route::match(['put', 'patch'], '/{identifier}', [ReferenceMasterController::class, 'update'])->defaults('master', 'services');
        Route::delete('/{identifier}', [ReferenceMasterController::class, 'destroy'])->defaults('master', 'services');
        Route::post('/{identifier}/restore', [ReferenceMasterController::class, 'restore'])->defaults('master', 'services');
        Route::delete('/{identifier}/force', [ReferenceMasterController::class, 'forceDelete'])->defaults('master', 'services');
    });

    Route::prefix('qualifications')->group(function () {
        Route::get('/', [ReferenceMasterController::class, 'index'])->defaults('master', 'qualifications');
        Route::get('/all', [ReferenceMasterController::class, 'all'])->defaults('master', 'qualifications');
        Route::get('/bin', [ReferenceMasterController::class, 'bin'])->defaults('master', 'qualifications');
        Route::post('/', [ReferenceMasterController::class, 'store'])->defaults('master', 'qualifications');
        Route::get('/{identifier}', [ReferenceMasterController::class, 'show'])->defaults('master', 'qualifications');
        Route::match(['put', 'patch'], '/{identifier}', [ReferenceMasterController::class, 'update'])->defaults('master', 'qualifications');
        Route::delete('/{identifier}', [ReferenceMasterController::class, 'destroy'])->defaults('master', 'qualifications');
        Route::post('/{identifier}/restore', [ReferenceMasterController::class, 'restore'])->defaults('master', 'qualifications');
        Route::delete('/{identifier}/force', [ReferenceMasterController::class, 'forceDelete'])->defaults('master', 'qualifications');
    });

    Route::get('/role/sidebar-menus', [RolePrivilegeController::class, 'sidebarMenusForRole']);
});
