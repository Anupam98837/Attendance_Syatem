<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_profile_id')->nullable()->constrained('employee_profiles')->nullOnDelete();
            $table->foreignId('attendance_id')->nullable()->constrained('employee_attendance')->nullOnDelete();
            $table->date('attendance_date')->nullable();

            // ── Session ──────────────────────────────────────────────────
            $table->timestamp('session_start')->nullable();      // first tracking event
            $table->timestamp('session_end')->nullable();        // last tracking event
            $table->string('platform', 20)->nullable();         // android | ios | web
            $table->string('app_version', 60)->nullable();
            $table->string('device_id', 120)->nullable();

            // ── GPS ──────────────────────────────────────────────────────
            $table->unsignedInteger('gps_connect_count')->default(0);     // GPS turned ON
            $table->unsignedInteger('gps_disconnect_count')->default(0);  // GPS turned OFF
            $table->json('gps_path')->nullable();
            // gps_path format: [{lat, lng, acc, ts, spd_kmh, alt, is_moving}]
            $table->unsignedInteger('total_distance_meters')->default(0);
            $table->boolean('is_traveling')->default(false);              // was >500m from office during day
            $table->unsignedInteger('time_traveling_minutes')->default(0);
            $table->unsignedInteger('time_stationary_minutes')->default(0);
            $table->decimal('max_speed_kmh', 6, 2)->nullable();
            $table->decimal('avg_speed_kmh', 6, 2)->nullable();
            $table->unsignedInteger('furthest_distance_from_office_meters')->nullable();

            // ── WiFi ─────────────────────────────────────────────────────
            $table->unsignedInteger('wifi_connect_count')->default(0);     // connected to any network
            $table->unsignedInteger('wifi_disconnect_count')->default(0);  // disconnected from any network
            $table->unsignedInteger('wifi_switch_count')->default(0);      // switched between SSIDs
            $table->json('wifi_networks_seen')->nullable();
            // wifi_networks_seen format: [{ssid, bssid, connected_at, disconnected_at, duration_seconds}]

            // ── Office / Geofence ─────────────────────────────────────────
            $table->unsignedInteger('office_entry_count')->default(0);
            $table->unsignedInteger('office_exit_count')->default(0);
            $table->unsignedInteger('time_inside_office_minutes')->default(0);
            $table->unsignedInteger('time_outside_office_minutes')->default(0);

            // ── Movement ──────────────────────────────────────────────────
            $table->unsignedInteger('movement_event_count')->default(0);   // total position changes
            $table->unsignedInteger('idle_streak_max_minutes')->default(0); // longest continuous idle period

            // ── App State ─────────────────────────────────────────────────
            $table->unsignedInteger('app_foreground_count')->default(0);   // times app came to foreground
            $table->unsignedInteger('app_background_count')->default(0);   // times app went to background
            $table->unsignedInteger('app_foreground_minutes')->default(0);
            $table->unsignedInteger('app_background_minutes')->default(0);

            // ── Battery ───────────────────────────────────────────────────
            $table->unsignedTinyInteger('battery_start_percent')->nullable();
            $table->unsignedTinyInteger('battery_end_percent')->nullable();

            // ── Sync metadata ─────────────────────────────────────────────
            $table->timestamp('client_created_at')->nullable();           // when the record was first created locally
            $table->timestamp('synced_at')->nullable();                   // when it reached the server
            $table->json('raw_events')->nullable();                       // raw event stream [{type,ts,payload}] - kept for debugging
            $table->json('metadata')->nullable();                         // any extra platform-specific data

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'attendance_date']);
            $table->index('attendance_id');
            $table->index('attendance_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_activity_logs');
    }
};
