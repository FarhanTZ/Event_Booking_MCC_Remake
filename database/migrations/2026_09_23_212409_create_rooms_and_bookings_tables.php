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
        // 1. Rooms Table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->integer('floor_level');
            $table->integer('capacity');
            $table->decimal('room_size_sqm', 6, 2)->nullable();
            $table->integer('buffer_minutes')->default(30);
            $table->boolean('is_commercial_allowed')->default(true);
            $table->decimal('base_hourly_rate', 12, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->text('description');
            $table->jsonb('supported_layouts')->default('["theater", "classroom", "u_shape"]');
            $table->timestamps();

            $table->index('floor_level');
            $table->index('is_active');
        });

        // 2. Bookings Table
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('booking_code', 30)->unique();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('status', 30)->default('draft'); // draft, awaiting_marketing, revised, awaiting_payment, confirmed, checked_in, completed, rejected, cancelled
            $table->string('clearance_status', 30)->default('pending'); // pending, cleared, incident_detected, abandoned_clean, abandoned_incident, resolved
            
            $table->string('pricing_scheme', 20)->default('subsidy');
            $table->decimal('nominal_amount', 12, 2)->default(0.00);
            $table->text('payment_notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignUuid('confirmed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('checkin_token', 64)->unique()->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignUuid('checked_in_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('completed_at')->nullable();
            $table->foreignUuid('completed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('revision_deadline')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();

            $table->index('status');
            $table->index('clearance_status');
            $table->index('booking_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('rooms');
    }
};
