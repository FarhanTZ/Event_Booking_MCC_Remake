<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_render_register_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Registrasi Akun Pemohon Acara');
    }

    public function test_can_register_as_instansi(): void
    {
        Storage::fake('public');

        $payload = [
            'account_type' => 'instansi',
            'full_name' => 'Budi Santoso',
            'email' => 'budi@ptkreatif.com',
            'phone_number' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'institution_name' => 'PT Malang Kreatif Digital',
            'website' => 'https://kreatif.id',
            'category' => 'Usaha/Bisnis',
            'sub_sector' => 'Aplikasi',
            'city' => 'Kota Malang',
            'district' => 'Lowokwaru',
            'address' => 'Jl. Soekarno Hatta No. 9',
            'description' => 'Software house di Kota Malang',
            'facebook' => 'https://facebook.com/kreatif',
            'instagram' => 'https://instagram.com/kreatif',
            'twitter' => '',
            'tiktok' => '',
            'youtube' => '',
            'logo' => UploadedFile::fake()->image('logo.png'),
            'terms' => 'on',
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'budi@ptkreatif.com',
            'account_type' => AccountType::INSTANSI->value,
            'institution_name' => 'PT Malang Kreatif Digital',
            'category' => 'Usaha/Bisnis',
            'sub_sector' => 'Aplikasi',
            'city' => 'Kota Malang',
            'district' => 'Lowokwaru',
        ]);
    }

    public function test_can_register_as_komunitas(): void
    {
        $payload = [
            'account_type' => 'komunitas',
            'full_name' => 'Rian Pratama',
            'email' => 'rian@komunitasdesain.id',
            'phone_number' => '082198765432',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'institution_name' => 'Komunitas Desain Grafis Malang',
            'address' => 'Jl. Veteran No. 10',
            'description' => 'Wadah desainer grafis muda',
            'instagram' => 'https://instagram.com/desainmalang',
            'terms' => 'on',
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'rian@komunitasdesain.id',
            'account_type' => AccountType::KOMUNITAS->value,
            'institution_name' => 'Komunitas Desain Grafis Malang',
        ]);
    }

    public function test_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@mcc.id',
            'password_hash' => 'secret1234',
            'account_type' => AccountType::KOMUNITAS,
            'full_name' => 'Ahmad Fauzi',
            'phone_number' => '+628111222333',
            'institution_name' => 'Komunitas Film Indie',
            'address' => 'Malang',
            'is_suspended' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'user@mcc.id',
            'password' => 'secret1234',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_can_register_as_sekolah(): void
    {
        $payload = [
            'account_type' => 'sekolah',
            'full_name' => 'Prof. Dr. Irwan',
            'email' => 'irwan@kampus.ac.id',
            'phone_number' => '085712345678',
            'password' => 'kampuspass123',
            'password_confirmation' => 'kampuspass123',
            'institution_name' => 'Universitas Negeri Malang - Fakultas Teknik',
            'address' => 'Jl. Semarang No. 5, Malang',
            'description' => 'Fakultas Teknik Informatika & Seni',
            'youtube' => 'https://youtube.com/@ftum',
            'terms' => 'on',
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'irwan@kampus.ac.id',
            'account_type' => AccountType::SEKOLAH->value,
            'institution_name' => 'Universitas Negeri Malang - Fakultas Teknik',
        ]);
    }

    public function test_cannot_login_if_user_is_suspended(): void
    {
        $user = User::factory()->create([
            'email' => 'suspended@mcc.id',
            'password_hash' => 'secret1234',
            'is_suspended' => true,
            'suspension_reason' => 'Pelanggaran SOP MCC',
        ]);

        $response = $this->post('/login', [
            'email' => 'suspended@mcc.id',
            'password' => 'secret1234',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_access_dashboard_with_clean_empty_state(): void
    {
        $user = User::factory()->create([
            'email' => 'organisasi@mcc.id',
            'password_hash' => 'secret1234',
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Kreatif Baru',
            'full_name' => 'Budi Santoso',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Portal Pemohon: Komunitas Kreatif Baru');
        $response->assertSee('Belum Ada Permohonan Ruangan');
        $response->assertSee('0 Pengajuan');
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
