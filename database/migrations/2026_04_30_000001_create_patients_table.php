<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();

            $table->string('phone_number', 32);
            $table->string('alternative_phone_number', 32)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('address')->nullable();

            $table->string('status', 20)->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->json('metadata')->nullable();

            $table->index('phone_number');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
