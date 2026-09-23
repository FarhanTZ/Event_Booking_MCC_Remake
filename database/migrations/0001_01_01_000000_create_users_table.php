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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('role', 30)->default('user'); // user, marketing, front_office, duty_officer, super_admin
            $table->string('account_type', 30)->default('komunitas'); // instansi, komunitas, sekolah

            // Identitas Personal & Kontak Akun
            $table->string('full_name', 150);
            $table->string('email', 150)->unique();
            $table->string('phone_number', 25)->unique();
            $table->string('password_hash');

            // Identitas Lembaga / Komunitas / Sekolah
            $table->string('institution_name', 150);
            $table->string('logo_url', 500)->nullable();
            $table->text('address');
            $table->text('description')->nullable();
            $table->jsonb('social_media')->default('{}');

            // Khusus Tipe Akun "Instansi" (Nullable untuk Komunitas & Sekolah)
            $table->string('website', 255)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('sub_sector', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();

            // Status Akun & Keamanan
            $table->boolean('is_suspended')->default(false);
            $table->text('suspension_reason')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Indexes
            $table->index('role');
            $table->index('account_type');
            $table->index('phone_number');
            $table->index('is_suspended');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};

