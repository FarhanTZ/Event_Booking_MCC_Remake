<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('booking_slots', function (Blueprint $table) {
            $table->foreignUuid('duty_officer_id')->nullable()->after('room_id')->constrained('users')->nullOnDelete();
            $table->timestamp('duty_officer_assigned_at')->nullable()->after('duty_officer_id');
            $table->foreignUuid('duty_officer_assigned_by')->nullable()->after('duty_officer_assigned_at')->constrained('users')->nullOnDelete();
            $table->text('duty_officer_notes')->nullable()->after('duty_officer_assigned_by');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignUuid('duty_officer_id')->nullable()->after('checked_in_by')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['duty_officer_id']);
            $table->dropColumn(['duty_officer_id']);
        });

        Schema::table('booking_slots', function (Blueprint $table) {
            $table->dropForeign(['duty_officer_id']);
            $table->dropForeign(['duty_officer_assigned_by']);
            $table->dropColumn([
                'duty_officer_id',
                'duty_officer_assigned_at',
                'duty_officer_assigned_by',
                'duty_officer_notes'
            ]);
        });
    }
};
