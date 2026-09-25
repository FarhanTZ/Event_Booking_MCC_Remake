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
        // 1. Indexes on booking_slots for triage sorting & date range filters
        Schema::table('booking_slots', function (Blueprint $table) {
            $table->index(['booking_id', 'event_start_at'], 'idx_slots_booking_start_at');
            $table->index('event_start_at', 'idx_slots_event_start_at');
        });

        // 2. Indexes on event_details for category, subsector & event queries
        Schema::table('event_details', function (Blueprint $table) {
            $table->index('event_category', 'idx_event_category');
            $table->index('ekraf_subsector', 'idx_event_ekraf_subsector');
        });

        // 3. Composite indexes on bookings for metric aggregations & user lookups
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['status', 'updated_at'], 'idx_bookings_status_updated_at');
            $table->index(['user_id', 'status'], 'idx_bookings_user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_user_status');
            $table->dropIndex('idx_bookings_status_updated_at');
        });

        Schema::table('event_details', function (Blueprint $table) {
            $table->dropIndex('idx_event_ekraf_subsector');
            $table->dropIndex('idx_event_category');
        });

        Schema::table('booking_slots', function (Blueprint $table) {
            $table->dropIndex('idx_slots_event_start_at');
            $table->dropIndex('idx_slots_booking_start_at');
        });
    }
};
