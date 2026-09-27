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
            $table->timestamp('do_notified_at')->nullable()->after('duty_officer_notes');
            $table->foreignUuid('do_notified_by')->nullable()->after('do_notified_at')->constrained('users')->nullOnDelete();
            $table->string('do_notified_channel', 30)->nullable()->after('do_notified_by'); // 'whatsapp_web', 'whatsapp_api', 'webhook'
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('do_notified_at')->nullable()->after('duty_officer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['do_notified_at']);
        });

        Schema::table('booking_slots', function (Blueprint $table) {
            $table->dropForeign(['do_notified_by']);
            $table->dropColumn([
                'do_notified_at',
                'do_notified_by',
                'do_notified_channel'
            ]);
        });
    }
};
