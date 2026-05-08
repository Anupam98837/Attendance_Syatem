<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_profile_id')->nullable()->constrained('employee_profiles')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('attendance_policy_id')->nullable()->constrained('attendance_policies')->nullOnDelete();
            $table->date('attendance_date')->nullable();
            $table->string('attendance_mode', 40)->nullable();
            $table->string('work_mode', 40)->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->decimal('check_out_latitude', 10, 7)->nullable();
            $table->decimal('check_out_longitude', 10, 7)->nullable();
            $table->unsignedInteger('gps_accuracy_meters')->nullable();
            $table->string('check_in_selfie_path')->nullable();
            $table->string('check_out_selfie_path')->nullable();
            $table->string('device_id', 120)->nullable();
            $table->string('request_ip', 64)->nullable();
            $table->unsignedInteger('total_working_minutes')->nullable();
            $table->unsignedInteger('late_minutes')->nullable();
            $table->unsignedInteger('early_checkout_minutes')->nullable();
            $table->unsignedInteger('overtime_minutes')->nullable();
            $table->unsignedInteger('break_minutes')->nullable();
            $table->string('status', 40)->nullable();
            $table->string('sync_status', 40)->nullable();
            $table->string('approval_status', 40)->nullable();
            $table->string('network_type', 40)->nullable();
            $table->string('wifi_ip_match', 120)->nullable();
            $table->boolean('within_geofence')->nullable();
            $table->boolean('within_wifi_ip')->nullable();
            $table->text('remarks')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'attendance_date', 'deleted_at'], 'employee_attendance_user_date_unique');
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employee_attendance_id')->nullable()->constrained('employee_attendance')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_profile_id')->nullable()->constrained('employee_profiles')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('attendance_policy_id')->nullable()->constrained('attendance_policies')->nullOnDelete();
            $table->string('punch_type', 40)->nullable();
            $table->timestamp('punch_time')->nullable();
            $table->timestamp('server_received_at')->nullable();
            $table->string('attendance_mode', 40)->nullable();
            $table->string('work_mode', 40)->nullable();
            $table->string('internet_status', 40)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('gps_accuracy_meters')->nullable();
            $table->string('location_text')->nullable();
            $table->string('selfie_path')->nullable();
            $table->decimal('face_match_score', 5, 2)->nullable();
            $table->string('device_id', 120)->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_platform', 40)->nullable();
            $table->string('app_version', 60)->nullable();
            $table->unsignedInteger('battery_level')->nullable();
            $table->string('network_type', 40)->nullable();
            $table->string('request_ip', 64)->nullable();
            $table->string('local_queue_id', 120)->nullable();
            $table->string('sync_status', 40)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->boolean('within_geofence')->nullable();
            $table->boolean('within_wifi_ip')->nullable();
            $table->boolean('is_exception')->nullable();
            $table->string('exception_reason')->nullable();
            $table->json('server_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'punch_time']);
            $table->unique(['user_id', 'local_queue_id', 'deleted_at'], 'attendance_logs_user_queue_unique');
        });

        Schema::create('attendance_location_tracks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employee_attendance_id')->constrained('employee_attendance')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('recorded_at')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('gps_accuracy_meters')->nullable();
            $table->unsignedInteger('battery_level')->nullable();
            $table->string('network_type', 40)->nullable();
            $table->boolean('is_background')->nullable();
            $table->decimal('speed_kmph', 8, 2)->nullable();
            $table->string('source', 40)->nullable();
            $table->string('sync_status', 40)->nullable();
            $table->string('local_queue_id', 120)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attendance_sync_queue', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_attendance_id')->nullable()->constrained('employee_attendance')->nullOnDelete();
            $table->string('local_queue_id', 120)->nullable();
            $table->string('queue_type', 40)->nullable();
            $table->string('attendance_mode', 40)->nullable();
            $table->string('device_id', 120)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->string('sync_status', 40)->nullable();
            $table->text('last_error')->nullable();
            $table->json('last_response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attendance_approvals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_attendance_id')->nullable()->constrained('employee_attendance')->nullOnDelete();
            $table->foreignId('attendance_log_id')->nullable()->constrained('attendance_logs')->nullOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approval_type', 60)->nullable();
            $table->string('status', 40)->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->text('reason')->nullable();
            $table->text('approver_remarks')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('snapshot')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_profile_id')->nullable()->constrained('employee_profiles')->nullOnDelete();
            $table->foreignId('leave_type_id')->nullable()->constrained('leave_types')->nullOnDelete();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->decimal('total_days', 8, 2)->nullable();
            $table->text('reason')->nullable();
            $table->string('status', 40)->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('module', 80)->nullable();
            $table->string('action', 80)->nullable();
            $table->string('target_type', 80)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('request_ip', 64)->nullable();
            $table->text('reason')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['module', 'action']);
            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('attendance_approvals');
        Schema::dropIfExists('attendance_sync_queue');
        Schema::dropIfExists('attendance_location_tracks');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('employee_attendance');
    }
};
