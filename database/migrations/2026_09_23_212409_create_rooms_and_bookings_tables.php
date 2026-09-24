<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 0. Aktifkan Ekstensi PostgreSQL yang Dibutuhkan Engine Anti-Bentrok
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
            DB::statement('CREATE EXTENSION IF NOT EXISTS "btree_gist";');
        }

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

        // 2. Room Images Table
        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('image_url', 500);
            $table->string('caption', 150)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('room_id');
        });

        // 3. Room Facilities Table
        Schema::create('room_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('facility_name', 100);
            $table->integer('quantity')->default(1);
            $table->string('unit', 20)->default('unit');
            $table->string('condition', 50)->default('good');
            $table->timestamp('created_at')->useCurrent();

            $table->index('room_id');
        });

        // 4. Bookings Table
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('booking_code', 30)->unique();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('status', 30)->default('draft');
            $table->string('clearance_status', 30)->default('pending');
            
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

        // 5. Booking Slots Table (Lengkap dengan TSRANGE & Exclusion Constraint)
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->timestamp('event_start_at');
            $table->timestamp('event_end_at');
            $table->boolean('is_active')->default(true);

            $table->index(['room_id', 'event_start_at', 'event_end_at']);
        });

        // Injeksi PostgreSQL Specific: TSRANGE, GiST Index, Constraint Anti Double-Booking, & Trigger Buffer
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE booking_slots ADD COLUMN operational_range TSRANGE NOT NULL;');
            DB::statement('CREATE INDEX idx_booking_slots_range ON booking_slots USING gist(operational_range);');
            
            DB::statement('
                ALTER TABLE booking_slots 
                ADD CONSTRAINT no_overlapping_room_slots 
                EXCLUDE USING gist (
                    room_id WITH =,
                    operational_range WITH &&
                ) WHERE (is_active = TRUE);
            ');

            DB::statement('
                CREATE OR REPLACE FUNCTION trg_calculate_operational_range()
                RETURNS TRIGGER AS $$
                DECLARE
                    v_buffer INT;
                BEGIN
                    SELECT buffer_minutes INTO v_buffer FROM rooms WHERE id = NEW.room_id;
                    IF v_buffer IS NULL THEN
                        v_buffer := 30;
                    END IF;

                    NEW.operational_range := tsrange(
                        NEW.event_start_at, 
                        NEW.event_end_at + (v_buffer || \' minutes\')::INTERVAL, 
                        \'[)\'
                    );
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
            ');

            DB::statement('
                CREATE TRIGGER trg_set_operational_range
                BEFORE INSERT OR UPDATE OF event_start_at, event_end_at, room_id
                ON booking_slots
                FOR EACH ROW
                EXECUTE FUNCTION trg_calculate_operational_range();
            ');
        } else {
            // Fallback column untuk SQLite memory testing
            Schema::table('booking_slots', function (Blueprint $table) {
                $table->string('operational_range')->nullable();
            });
        }

        // 6. Event Details Table
        Schema::create('event_details', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->string('event_name', 200);
            $table->string('event_category', 100);
            $table->string('ekraf_subsector', 100)->nullable();
            $table->string('event_type', 50)->nullable();
            $table->integer('estimated_participants')->default(0);
            $table->text('event_description');
            $table->text('equipment_needed')->nullable();
            $table->jsonb('requested_facilities')->default('[]');
            $table->string('proposal_file_url', 500);
            $table->string('poster_image_url', 500)->nullable();
            $table->string('pic_name_snapshot', 150);
            $table->string('pic_phone_snapshot', 25);
            $table->string('institution_name_snapshot', 150);
            $table->timestamp('tos_agreed_at');
            $table->timestamp('created_at')->useCurrent();
        });

        // 7. Booking Approvals Table
        Schema::create('booking_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignUuid('action_by')->constrained('users')->cascadeOnDelete();
            $table->string('decision', 30);
            $table->text('notes');
            $table->timestamp('created_at')->useCurrent();
        });

        // 8. Room Incidents Table
        Schema::create('room_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignUuid('reported_by_do')->constrained('users')->cascadeOnDelete();
            $table->string('category', 50);
            $table->string('item_name', 150)->nullable();
            $table->text('description');
            $table->boolean('is_resolved')->default(false);
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 9. Incident Photos Table
        Schema::create('incident_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('room_incidents')->cascadeOnDelete();
            $table->string('photo_url', 500);
            $table->string('caption', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 10. KTP Vault Logs Table
        Schema::create('ktp_vault_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('ktp_holder_name', 150);
            $table->string('nik_number', 255);
            $table->string('ktp_photo_scan_url', 500)->nullable();
            $table->foreignUuid('received_by_fo')->constrained('users')->cascadeOnDelete();
            $table->timestamp('retained_at')->useCurrent();
            $table->foreignUuid('returned_by_fo')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('returned_at')->nullable();
            $table->string('unclaimed_box_id', 50)->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP TRIGGER IF EXISTS trg_set_operational_range ON booking_slots;');
            DB::statement('DROP FUNCTION IF EXISTS trg_calculate_operational_range;');
        }

        Schema::dropIfExists('ktp_vault_logs');
        Schema::dropIfExists('incident_photos');
        Schema::dropIfExists('room_incidents');
        Schema::dropIfExists('booking_approvals');
        Schema::dropIfExists('event_details');
        Schema::dropIfExists('booking_slots');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('room_facilities');
        Schema::dropIfExists('room_images');
        Schema::dropIfExists('rooms');
    }
};
