<?php

use App\Http\Controllers\PublicDirectoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicDirectoryController::class, 'home'])->name('directory.home');
Route::get('/login', fn () => view('pages.auth.login'))->name('login');
Route::get('/register', fn () => view('pages.auth.register'))->name('register');
Route::get('/find-doctors/departments', [PublicDirectoryController::class, 'departments'])->name('directory.departments.index');
Route::get('/find-doctors/departments/{slug}', [PublicDirectoryController::class, 'departmentShow'])->name('directory.departments.show');
Route::get('/doctor/{slug}', [PublicDirectoryController::class, 'doctorShow'])->name('directory.doctors.show');

Route::view('/dashboard', 'pages.pages.common.dashboard');
Route::view('/profile', 'pages.pages.user.profile');
Route::view('/my-bookings', 'pages.pages.bookings.myBookings');
Route::view('/bookings/manage', 'pages.pages.bookings.manageBookings');
Route::redirect('/user', '/users/manage');

Route::view('/users/manage', 'pages.pages.users.manageUsers');
Route::view('/user/manage', 'pages.pages.users.manageUsers');

Route::view('/dashboard-menu/manage', 'modules.dashboardMenu.manageDashboardMenu');
Route::view('/dashboard-menu/create', 'modules.dashboardMenu.createDashboardMenu');

Route::view('/page-privilege/manage', 'modules.privileges.managePagePrivileges');
Route::view('/page-privilege/create', 'modules.privileges.createPagePrivileges');

Route::view('/user-privileges/manage', 'modules.privileges.assignPrivileges');
Route::view('/role-privileges/manage', 'modules.privileges.assignRolePrivileges');
Route::view('/departments', 'pages.pages.departments.manageDepartments');
Route::view('/departments/create', 'pages.pages.departments.manageDepartments');
Route::view('/clinics/manage', 'pages.pages.clinics.manageClinics');
Route::view('/hospital/create', 'pages.pages.hospitals.createHospital');
Route::view('/hospital/manage', 'pages.pages.hospitals.manageHospitals');
Route::view('/doctor/profile/{userUuid}', 'pages.pages.doctors.profileDoctor');
Route::view('/specializations/manage', 'pages.pages.specializations.manageSpecializations');
Route::view('/designations/manage', 'pages.pages.designations.manageDesignations');
Route::view('/registration-councils/manage', 'pages.pages.registrationCouncils.manageRegistrationCouncils');
Route::view('/languages/manage', 'pages.pages.languages.manageLanguages');
Route::view('/services/manage', 'pages.pages.services.manageServices');
Route::view('/qualifications/manage', 'pages.pages.qualifications.manageQualifications');
