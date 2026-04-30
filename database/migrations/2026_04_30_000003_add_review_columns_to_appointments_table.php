<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->text('status_note')->nullable()->after('status');
            $table->foreignId('reviewed_by_user_id')->nullable()->after('status_note')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by_user_id');
            $table->timestamp('cancelled_at')->nullable()->after('reviewed_at');

            $table->index('reviewed_by_user_id');
            $table->index('reviewed_at');
            $table->index('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['reviewed_by_user_id']);
            $table->dropIndex(['reviewed_at']);
            $table->dropIndex(['cancelled_at']);
            $table->dropConstrainedForeignId('reviewed_by_user_id');
            $table->dropColumn(['status_note', 'reviewed_at', 'cancelled_at']);
        });
    }
};
