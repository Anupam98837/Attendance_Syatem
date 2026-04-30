<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('booked_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->foreignId('doctor_clinic_id')->nullable()->constrained('doctor_clinics')->nullOnDelete();

            $table->string('booking_for', 20)->default('self');
            $table->string('relationship_with_patient', 100)->nullable();

            $table->date('appointment_date');
            $table->time('appointment_time')->nullable();
            $table->string('consultation_mode', 30)->default('clinic_visit');

            $table->string('status', 20)->default('pending');
            $table->text('symptoms')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['booked_by_user_id', 'appointment_date']);
            $table->index(['doctor_id', 'appointment_date']);
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
