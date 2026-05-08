<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('employee_code', 64)->nullable()->unique();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('attendance_policy_id')->nullable()->constrained('attendance_policies')->nullOnDelete();
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employment_type', 40)->nullable();
            $table->string('work_mode', 40)->nullable();
            $table->date('join_date')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->date('exit_date')->nullable();
            $table->boolean('offline_attendance_enabled')->nullable();
            $table->boolean('field_attendance_enabled')->nullable();
            $table->boolean('wfh_attendance_enabled')->nullable();
            $table->boolean('continuous_tracking_enabled')->nullable();
            $table->string('face_image_path')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'deleted_at'], 'employee_profiles_user_active_unique');
        });

        Schema::create('device_registrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_profile_id')->nullable()->constrained('employee_profiles')->nullOnDelete();
            $table->string('device_id', 120);
            $table->string('device_name')->nullable();
            $table->string('device_platform', 40)->nullable();
            $table->string('device_model', 120)->nullable();
            $table->string('os_version', 60)->nullable();
            $table->string('app_version', 60)->nullable();
            $table->string('push_token')->nullable();
            $table->string('last_ip', 64)->nullable();
            $table->timestamp('bound_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'device_id', 'deleted_at'], 'device_registrations_user_device_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_registrations');
        Schema::dropIfExists('employee_profiles');
    }
};
