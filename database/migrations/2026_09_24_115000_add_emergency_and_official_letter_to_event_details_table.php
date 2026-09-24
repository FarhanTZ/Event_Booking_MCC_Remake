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
        Schema::table('event_details', function (Blueprint $table) {
            $table->string('pic_emergency_phone', 25)->nullable()->after('pic_phone_snapshot');
            $table->string('official_letter_number', 100)->nullable()->after('proposal_file_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_details', function (Blueprint $table) {
            $table->dropColumn(['pic_emergency_phone', 'official_letter_number']);
        });
    }
};
