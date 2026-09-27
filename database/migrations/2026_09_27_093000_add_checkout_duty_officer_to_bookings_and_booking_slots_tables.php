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
            if (! Schema::hasColumn('bookings', 'assigned_checkout_do_id')) {
                $table->foreignUuid('assigned_checkout_do_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'checkout_duty_officer_id')) {
                $table->foreignUuid('checkout_duty_officer_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'checkout_do_assigned_at')) {
                $table->timestamp('checkout_do_assigned_at')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'checkout_initiated_at')) {
                $table->timestamp('checkout_initiated_at')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'checkout_do_assigned_by')) {
                $table->foreignUuid('checkout_do_assigned_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'checkout_initiated_by')) {
                $table->foreignUuid('checkout_initiated_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'checkout_do_notes')) {
                $table->text('checkout_do_notes')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'checkout_do_notified_at')) {
                $table->timestamp('checkout_do_notified_at')->nullable();
            }
        });

        Schema::table('booking_slots', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_slots', 'assigned_checkout_do_id')) {
                $table->foreignUuid('assigned_checkout_do_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('booking_slots', 'checkout_duty_officer_id')) {
                $table->foreignUuid('checkout_duty_officer_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('booking_slots', 'checkout_do_assigned_at')) {
                $table->timestamp('checkout_do_assigned_at')->nullable();
            }
            if (! Schema::hasColumn('booking_slots', 'checkout_initiated_at')) {
                $table->timestamp('checkout_initiated_at')->nullable();
            }
            if (! Schema::hasColumn('booking_slots', 'checkout_do_assigned_by')) {
                $table->foreignUuid('checkout_do_assigned_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('booking_slots', 'checkout_initiated_by')) {
                $table->foreignUuid('checkout_initiated_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('booking_slots', 'checkout_do_notes')) {
                $table->text('checkout_do_notes')->nullable();
            }
            if (! Schema::hasColumn('booking_slots', 'checkout_do_notified_at')) {
                $table->timestamp('checkout_do_notified_at')->nullable();
            }
            if (! Schema::hasColumn('booking_slots', 'checkout_do_notified_by')) {
                $table->foreignUuid('checkout_do_notified_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('booking_slots', 'checkout_do_notified_channel')) {
                $table->string('checkout_do_notified_channel', 30)->nullable();
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
            if (Schema::hasColumn('booking_slots', 'assigned_checkout_do_id')) {
                $table->dropForeign(['assigned_checkout_do_id']);
                $dropCols[] = 'assigned_checkout_do_id';
            }
            if (Schema::hasColumn('booking_slots', 'checkout_do_assigned_by')) {
                $table->dropForeign(['checkout_do_assigned_by']);
                $dropCols[] = 'checkout_do_assigned_by';
            }
            if (Schema::hasColumn('booking_slots', 'checkout_do_notified_by')) {
                $table->dropForeign(['checkout_do_notified_by']);
                $dropCols[] = 'checkout_do_notified_by';
            }
            if (Schema::hasColumn('booking_slots', 'checkout_do_assigned_at')) $dropCols[] = 'checkout_do_assigned_at';
            if (Schema::hasColumn('booking_slots', 'checkout_do_notified_channel')) $dropCols[] = 'checkout_do_notified_channel';
            
            if (! empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('bookings', 'assigned_checkout_do_id')) {
                $table->dropForeign(['assigned_checkout_do_id']);
                $dropCols[] = 'assigned_checkout_do_id';
            }
            if (Schema::hasColumn('bookings', 'checkout_do_assigned_by')) {
                $table->dropForeign(['checkout_do_assigned_by']);
                $dropCols[] = 'checkout_do_assigned_by';
            }
            if (Schema::hasColumn('bookings', 'checkout_do_assigned_at')) $dropCols[] = 'checkout_do_assigned_at';
            if (Schema::hasColumn('bookings', 'checkout_do_notes')) $dropCols[] = 'checkout_do_notes';
            
            if (! empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
