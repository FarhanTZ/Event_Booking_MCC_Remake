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
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'room_opened_at')) {
                $table->timestamp('room_opened_at')->nullable()->after('checked_in_by');
            }
            if (! Schema::hasColumn('bookings', 'room_opened_by')) {
                $table->foreignUuid('room_opened_by')->nullable()->after('room_opened_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'room_open_notes')) {
                $table->text('room_open_notes')->nullable()->after('room_opened_by');
            }
            if (! Schema::hasColumn('bookings', 'facility_checklist')) {
                $table->jsonb('facility_checklist')->nullable()->after('room_open_notes');
            }
        });

        Schema::table('booking_slots', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_slots', 'room_opened_at')) {
                $table->timestamp('room_opened_at')->nullable()->after('duty_officer_notes');
            }
            if (! Schema::hasColumn('booking_slots', 'room_opened_by')) {
                $table->foreignUuid('room_opened_by')->nullable()->after('room_opened_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('booking_slots', 'room_open_notes')) {
                $table->text('room_open_notes')->nullable()->after('room_opened_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_slots', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('booking_slots', 'room_opened_by')) {
                $table->dropForeign(['room_opened_by']);
                $dropCols[] = 'room_opened_by';
            }
            if (Schema::hasColumn('booking_slots', 'room_opened_at')) $dropCols[] = 'room_opened_at';
            if (Schema::hasColumn('booking_slots', 'room_open_notes')) $dropCols[] = 'room_open_notes';
            
            if (! empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('bookings', 'room_opened_by')) {
                $table->dropForeign(['room_opened_by']);
                $dropCols[] = 'room_opened_by';
            }
            if (Schema::hasColumn('bookings', 'room_opened_at')) $dropCols[] = 'room_opened_at';
            if (Schema::hasColumn('bookings', 'room_open_notes')) $dropCols[] = 'room_open_notes';
            if (Schema::hasColumn('bookings', 'facility_checklist')) $dropCols[] = 'facility_checklist';
            
            if (! empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
