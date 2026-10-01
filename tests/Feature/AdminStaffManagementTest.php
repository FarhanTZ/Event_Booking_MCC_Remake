<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminStaffManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $marketingUser;
    private User $frontOfficeUser;
    private User $dutyOfficerUser;
    private User $publicUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'full_name' => 'Super Admin Test',
            'email' => 'superadmin@mcc.test',
            'phone_number' => '081111111111',
            'role' => UserRole::SUPER_ADMIN,
            'is_suspended' => false,
        ]);

        $this->marketingUser = User::factory()->create([
            'full_name' => 'Marketing Staff',
            'email' => 'marketing@mcc.test',
            'phone_number' => '082222222222',
            'role' => UserRole::MARKETING,
            'is_suspended' => false,
        ]);

        $this->frontOfficeUser = User::factory()->create([
            'full_name' => 'Front Office Staff',
            'email' => 'frontoffice@mcc.test',
            'phone_number' => '083333333333',
            'role' => UserRole::FRONT_OFFICE,
            'is_suspended' => false,
        ]);

        $this->dutyOfficerUser = User::factory()->create([
            'full_name' => 'Duty Officer Staff',
            'email' => 'dutyofficer@mcc.test',
            'phone_number' => '084444444444',
            'role' => UserRole::DUTY_OFFICER,
            'is_suspended' => false,
        ]);

        $this->publicUser = User::factory()->create([
            'full_name' => 'Public User',
            'email' => 'user@mcc.test',
            'phone_number' => '085555555555',
            'role' => UserRole::USER,
            'is_suspended' => false,
        ]);
    }

    public function test_guest_cannot_access_staff_management(): void
    {
        $response = $this->get(route('admin.staff.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_super_admin_cannot_access_staff_management(): void
    {
        $response = $this->actingAs($this->marketingUser)->get(route('admin.staff.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->publicUser)->get(route('admin.staff.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_view_staff_list(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.staff.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Akun Staf Lintas Divisi');
        $response->assertSee('Marketing Staff');
        $response->assertSee('Front Office Staff');
        $response->assertSee('Duty Officer Staff');
        // Regular public user should not appear in staff list by default
        $response->assertDontSee('Public User');
    }

    public function test_super_admin_can_filter_staff_by_role_and_search(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.staff.index', [
            'role' => 'marketing',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Marketing Staff');
        $response->assertDontSee('Front Office Staff');

        $response = $this->actingAs($this->superAdmin)->get(route('admin.staff.index', [
            'search' => 'Duty Officer',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Duty Officer Staff');
        $response->assertDontSee('Marketing Staff');
    }

    public function test_super_admin_can_create_new_staff_account(): void
    {
        $createPageResponse = $this->actingAs($this->superAdmin)->get(route('admin.staff.create'));
        $createPageResponse->assertStatus(200);
        $createPageResponse->assertSee('Tambah Akun Staf Baru');

        $storeResponse = $this->actingAs($this->superAdmin)->post(route('admin.staff.store'), [
            'name' => 'Ahmad Kasir FO',
            'email' => 'ahmad.fo@mcc.test',
            'phone_number' => '08999888777',
            'role' => 'front_office',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $storeResponse->assertRedirect(route('admin.staff.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'full_name' => 'Ahmad Kasir FO',
            'email' => 'ahmad.fo@mcc.test',
            'phone_number' => '08999888777',
            'role' => UserRole::FRONT_OFFICE->value,
            'is_suspended' => false,
        ]);

        $createdUser = User::where('email', 'ahmad.fo@mcc.test')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue(Hash::check('SecurePass123!', $createdUser->password_hash));
    }

    public function test_create_staff_validation_errors(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.staff.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'phone_number' => 'abc',
            'role' => 'invalid_role',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['full_name', 'email', 'phone_number', 'role', 'password']);
    }

    public function test_super_admin_can_update_staff_profile_and_role(): void
    {
        $editPageResponse = $this->actingAs($this->superAdmin)->get(route('admin.staff.edit', $this->marketingUser->id));
        $editPageResponse->assertStatus(200);
        $editPageResponse->assertSee('Perbarui Akun: Marketing Staff');

        $updateResponse = $this->actingAs($this->superAdmin)->put(route('admin.staff.update', $this->marketingUser->id), [
            'name' => 'Marketing Lead Renamed',
            'email' => 'marketing.lead@mcc.test',
            'phone_number' => '082222229999',
            'role' => 'duty_officer',
        ]);

        $updateResponse->assertRedirect(route('admin.staff.index'));
        $updateResponse->assertSessionHas('success');

        $this->marketingUser->refresh();
        $this->assertEquals('Marketing Lead Renamed', $this->marketingUser->name);
        $this->assertEquals('marketing.lead@mcc.test', $this->marketingUser->email);
        $this->assertEquals('082222229999', $this->marketingUser->phone_number);
        $this->assertEquals(UserRole::DUTY_OFFICER, $this->marketingUser->role);
    }

    public function test_super_admin_can_reset_staff_password(): void
    {
        $resetResponse = $this->actingAs($this->superAdmin)->patch(route('admin.staff.reset_password', $this->frontOfficeUser->id), [
            'password' => 'NewSecretPassword123!',
            'password_confirmation' => 'NewSecretPassword123!',
        ]);

        $resetResponse->assertRedirect(route('admin.staff.index'));
        $resetResponse->assertSessionHas('success');

        $this->frontOfficeUser->refresh();
        $this->assertTrue(Hash::check('NewSecretPassword123!', $this->frontOfficeUser->password_hash));
    }

    public function test_super_admin_can_deactivate_and_reactivate_staff_account(): void
    {
        // Deactivate staff
        $deactivateResponse = $this->actingAs($this->superAdmin)->patch(route('admin.staff.toggle_status', $this->dutyOfficerUser->id), [
            'action' => 'deactivate',
            'suspension_reason' => 'Mutasi ke dinas perindustrian kota',
        ]);

        $deactivateResponse->assertRedirect(route('admin.staff.index'));
        $this->dutyOfficerUser->refresh();
        $this->assertTrue($this->dutyOfficerUser->is_suspended);
        $this->assertEquals('Mutasi ke dinas perindustrian kota', $this->dutyOfficerUser->suspension_reason);
        $this->assertNotNull($this->dutyOfficerUser->suspended_at);

        // Reactivate staff
        $reactivateResponse = $this->actingAs($this->superAdmin)->patch(route('admin.staff.toggle_status', $this->dutyOfficerUser->id), [
            'action' => 'activate',
        ]);

        $reactivateResponse->assertRedirect(route('admin.staff.index'));
        $this->dutyOfficerUser->refresh();
        $this->assertFalse($this->dutyOfficerUser->is_suspended);
        $this->assertNull($this->dutyOfficerUser->suspension_reason);
        $this->assertNull($this->dutyOfficerUser->suspended_at);
    }

    public function test_super_admin_cannot_deactivate_or_delete_own_account(): void
    {
        // Attempt deactivate self
        $toggleSelfResponse = $this->actingAs($this->superAdmin)->patch(route('admin.staff.toggle_status', $this->superAdmin->id), [
            'action' => 'deactivate',
            'suspension_reason' => 'Self deactivation test',
        ]);

        $toggleSelfResponse->assertRedirect(route('admin.staff.index'));
        $toggleSelfResponse->assertSessionHas('error');
        $this->superAdmin->refresh();
        $this->assertFalse($this->superAdmin->is_suspended);

        // Attempt delete self
        $deleteSelfResponse = $this->actingAs($this->superAdmin)->delete(route('admin.staff.destroy', $this->superAdmin->id));
        $deleteSelfResponse->assertRedirect(route('admin.staff.index'));
        $deleteSelfResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
    }

    public function test_super_admin_can_delete_staff_account(): void
    {
        $deleteResponse = $this->actingAs($this->superAdmin)->delete(route('admin.staff.destroy', $this->dutyOfficerUser->id));

        $deleteResponse->assertRedirect(route('admin.staff.index'));
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $this->dutyOfficerUser->id]);
    }
}
