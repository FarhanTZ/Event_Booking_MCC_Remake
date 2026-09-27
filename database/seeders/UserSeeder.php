<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed internal roles and demo public user into the database idempotently.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');

        // 1. Marketing & Kurasi Officer
        User::firstOrCreate(
            ['email' => 'marketing@mcc.id'],
            [
                'full_name' => 'Tim Kurasi & Marketing MCC',
                'phone_number' => '+6281234567890',
                'password_hash' => $defaultPassword,
                'role' => UserRole::MARKETING,
                'account_type' => AccountType::INSTANSI,
                'institution_name' => 'Malang Creative Center (UPTD MCC)',
                'address' => 'Jl. Jenderal Ahmad Yani No. 53, Blimbing, Kota Malang',
                'category' => 'Lembaga Pemerintah',
                'city' => 'Kota Malang',
                'district' => 'Blimbing',
            ]
        );

        // 2. Front Office / Kustodian KTP
        User::firstOrCreate(
            ['email' => 'frontoffice@mcc.id'],
            [
                'full_name' => 'Front Office & Kustodian KTP MCC',
                'phone_number' => '+6281234567891',
                'password_hash' => $defaultPassword,
                'role' => UserRole::FRONT_OFFICE,
                'account_type' => AccountType::INSTANSI,
                'institution_name' => 'Front Office MCC',
                'address' => 'Lantai 1 Meja Registrasi, MCC Malang',
                'category' => 'Lembaga Pemerintah',
                'city' => 'Kota Malang',
                'district' => 'Blimbing',
            ]
        );

        // 3. Duty Officer / Inspeksi Ruangan Tim MCC
        User::firstOrCreate(
            ['email' => 'dutyofficer@mcc.id'],
            [
                'full_name' => 'Bambang Sudiro, S.T.',
                'phone_number' => '+6281234567892',
                'password_hash' => $defaultPassword,
                'role' => UserRole::DUTY_OFFICER,
                'account_type' => AccountType::INSTANSI,
                'institution_name' => 'Divisi Operasional & Inspeksi Gedung MCC',
                'address' => 'Gedung MCC Lantai 2, Kota Malang',
                'category' => 'Lembaga Pemerintah',
                'city' => 'Kota Malang',
                'district' => 'Blimbing',
            ]
        );

        User::firstOrCreate(
            ['email' => 'do.hendra@mcc.id'],
            [
                'full_name' => 'Hendra Kurniawan',
                'phone_number' => '+6281234567893',
                'password_hash' => $defaultPassword,
                'role' => UserRole::DUTY_OFFICER,
                'account_type' => AccountType::INSTANSI,
                'institution_name' => 'Divisi Operasional & Inspeksi Gedung MCC',
                'address' => 'Gedung MCC Lantai 4, Kota Malang',
                'category' => 'Lembaga Pemerintah',
                'city' => 'Kota Malang',
                'district' => 'Blimbing',
            ]
        );

        User::firstOrCreate(
            ['email' => 'do.rizky@mcc.id'],
            [
                'full_name' => 'Rizky Pratama',
                'phone_number' => '+6281234567894',
                'password_hash' => $defaultPassword,
                'role' => UserRole::DUTY_OFFICER,
                'account_type' => AccountType::INSTANSI,
                'institution_name' => 'Divisi Operasional & Inspeksi Gedung MCC',
                'address' => 'Gedung MCC Lantai 7, Kota Malang',
                'category' => 'Lembaga Pemerintah',
                'city' => 'Kota Malang',
                'district' => 'Blimbing',
            ]
        );

        User::firstOrCreate(
            ['email' => 'do.patroli@mcc.id'],
            [
                'full_name' => 'Ahmad Faisol',
                'phone_number' => '+6281234567895',
                'password_hash' => $defaultPassword,
                'role' => UserRole::DUTY_OFFICER,
                'account_type' => AccountType::INSTANSI,
                'institution_name' => 'Divisi Operasional & Inspeksi Gedung MCC',
                'address' => 'Gedung MCC, Kota Malang',
                'category' => 'Lembaga Pemerintah',
                'city' => 'Kota Malang',
                'district' => 'Blimbing',
            ]
        );

        // 4. Super Administrator
        User::firstOrCreate(
            ['email' => 'admin@mcc.id'],
            [
                'full_name' => 'Super Administrator MCC',
                'phone_number' => '+6281234567899',
                'password_hash' => $defaultPassword,
                'role' => UserRole::SUPER_ADMIN,
                'account_type' => AccountType::INSTANSI,
                'institution_name' => 'Diskopindag Kota Malang',
                'address' => 'Balai Kota Malang',
                'category' => 'Lembaga Pemerintah',
                'city' => 'Kota Malang',
                'district' => 'Klojen',
            ]
        );

        // 5. Demo Public User
        User::firstOrCreate(
            ['email' => 'demo@mcc.id'],
            [
                'full_name' => 'Demo User Komunitas',
                'phone_number' => '+6281298765432',
                'password_hash' => $defaultPassword,
                'role' => UserRole::USER,
                'account_type' => AccountType::KOMUNITAS,
                'institution_name' => 'Komunitas Kreatif Arema Digital',
                'address' => 'Klojen, Kota Malang',
                'description' => 'Komunitas pegiat desain grafis, animasi, dan multimedia kota Malang.',
            ]
        );
    }
}
