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
        Schema::table('ktp_vault_logs', function (Blueprint $table) {
            $table->string('nik_number', 255)->nullable()->change();
            $table->string('pic_whatsapp_number', 30)->nullable()->after('ktp_holder_name');
            $table->string('storage_drawer_number', 50)->nullable()->after('pic_whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ktp_vault_logs', function (Blueprint $table) {
            $table->dropColumn(['pic_whatsapp_number', 'storage_drawer_number']);
            $table->string('nik_number', 255)->nullable(false)->change();
        });
    }
};
