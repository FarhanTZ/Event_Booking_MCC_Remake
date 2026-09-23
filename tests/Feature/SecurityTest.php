<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Services\FileUploadSecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_brute_force_is_throttled_after_consecutive_failed_attempts(): void
    {
        $email = 'victim@mcc.id';

        // 5 Failed Login Attempts
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'wrongpassword123',
            ]);
            $response->assertSessionHasErrors('email');
        }

        // 6th Attempt should be blocked by Rate Limiter
        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'wrongpassword123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->get('errors')->first('email') !== 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.');
        $this->assertStringContainsString('Terlalu banyak percobaan', session()->get('errors')->first('email'));
    }

    public function test_booking_policy_prevents_idor_cross_user_access(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();

        $booking = Booking::create([
            'booking_code' => 'MCC-2026-TEST1',
            'user_id' => $owner->id,
            'status' => 'draft',
        ]);

        $policy = new BookingPolicy();

        // Owner CAN view
        $this->assertTrue($policy->view($owner, $booking));

        // Stranger CANNOT view
        $this->assertFalse($policy->view($stranger, $booking));

        // Stranger CANNOT update
        $this->assertFalse($policy->update($stranger, $booking));
    }

    public function test_registration_rejects_numeric_only_or_weak_passwords(): void
    {
        // 1. Password angka saja (contoh: 12345658) harus DITOLAK
        $response = $this->post('/register', [
            'account_type' => 'komunitas',
            'full_name' => 'Testing User',
            'email' => 'weakpw@mcc.id',
            'phone_number' => '081234567800',
            'password' => '12345658',
            'password_confirmation' => '12345658',
            'institution_name' => 'Komunitas Testing',
            'address' => 'Malang',
            'terms' => 'on',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();

        // 2. Password huruf saja tanpa angka (contoh: passwordaja) harus DITOLAK
        $responseLettersOnly = $this->post('/register', [
            'account_type' => 'komunitas',
            'full_name' => 'Testing User',
            'email' => 'weakpw2@mcc.id',
            'phone_number' => '081234567801',
            'password' => 'passwordaja',
            'password_confirmation' => 'passwordaja',
            'institution_name' => 'Komunitas Testing',
            'address' => 'Malang',
            'terms' => 'on',
        ]);

        $responseLettersOnly->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
