<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('company_name')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('company_code', 64)->nullable()->unique();
            $table->string('timezone', 64)->nullable();
            $table->json('working_days')->nullable();
            $table->json('weekly_offs')->nullable();
            $table->string('attendance_mode', 40)->nullable();
            $table->unsignedInteger('default_grace_time_minutes')->nullable();
            $table->unsignedInteger('offline_sync_limit_hours')->nullable();
            $table->string('default_currency', 12)->nullable();
            $table->string('date_format', 32)->nullable();
            $table->string('time_format', 32)->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 64)->nullable()->unique();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'deleted_at']);
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 64)->nullable()->unique();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'deleted_at']);
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 64)->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('city', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('postal_code', 32)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('geofence_radius_meters')->nullable();
            $table->boolean('wifi_only')->nullable();
            $table->boolean('allow_mobile_data')->nullable();
            $table->boolean('allow_outside_geofence')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'deleted_at']);
        });

        Schema::create('branch_allowed_networks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('ip_pattern', 120);
            $table->string('network_type', 32)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['branch_id', 'ip_pattern', 'deleted_at'], 'branch_allowed_networks_unique_pattern');
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 64)->nullable()->unique();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('allow_cross_day')->nullable();
            $table->unsignedInteger('grace_minutes')->nullable();
            $table->time('late_after_time')->nullable();
            $table->unsignedInteger('half_day_working_minutes')->nullable();
            $table->unsignedInteger('full_day_working_minutes')->nullable();
            $table->unsignedInteger('minimum_working_minutes')->nullable();
            $table->unsignedInteger('overtime_after_minutes')->nullable();
            $table->unsignedInteger('early_checkout_before_minutes')->nullable();
            $table->unsignedInteger('break_minutes')->nullable();
            $table->json('week_days')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'deleted_at']);
        });

        Schema::create('attendance_policies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 64)->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('gps_required')->nullable();
            $table->boolean('selfie_required')->nullable();
            $table->boolean('checkout_selfie_required')->nullable();
            $table->string('face_verification_mode', 40)->nullable();
            $table->boolean('liveness_required')->nullable();
            $table->boolean('offline_attendance_allowed')->nullable();
            $table->unsignedInteger('offline_sync_limit_hours')->nullable();
            $table->unsignedInteger('offline_sync_limit_minutes')->nullable();
            $table->boolean('multiple_punch_allowed')->nullable();
            $table->boolean('geofence_required')->nullable();
            $table->boolean('outside_location_allowed')->nullable();
            $table->boolean('outside_location_requires_approval')->nullable();
            $table->boolean('device_binding_required')->nullable();
            $table->boolean('wifi_ip_restriction_required')->nullable();
            $table->boolean('allow_mobile_data')->nullable();
            $table->boolean('qr_attendance_enabled')->nullable();
            $table->boolean('mark_gps_missing_as_exception')->nullable();
            $table->boolean('auto_approve_clean_records')->nullable();
            $table->unsignedInteger('time_drift_tolerance_minutes')->nullable();
            $table->unsignedInteger('max_offline_records_per_day')->nullable();
            $table->unsignedInteger('continuous_tracking_interval_seconds')->nullable();
            $table->boolean('allow_field_attendance')->nullable();
            $table->boolean('allow_wfh_attendance')->nullable();
            $table->boolean('require_work_note_for_wfh')->nullable();
            $table->json('allowed_punch_types')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'deleted_at']);
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->date('holiday_date')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('holiday_type', 40)->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 64)->nullable()->unique();
            $table->decimal('days_allowed', 8, 2)->nullable();
            $table->boolean('is_paid')->nullable();
            $table->boolean('requires_approval')->nullable();
            $table->boolean('requires_document')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('attendance_policies');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('branch_allowed_networks');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('company_settings');
    }
};
