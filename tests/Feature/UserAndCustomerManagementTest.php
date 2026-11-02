<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\SportMatch;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserAndCustomerManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        User::where('email', 'selam@gurabicha.com')->delete();
        Customer::where('phone', '+251911998877')->delete();
    }

    public function test_staff_with_must_change_password_is_redirected_to_change_password(): void
    {
        $staff = User::where('email', 'staff@gurabicha.com')->first();
        $this->assertTrue((bool)$staff->must_change_password);

        // Attempting to visit dashboard should redirect to change password page
        $response = $this->actingAs($staff)->get('/admin/dashboard');
        $response->assertRedirect(route('admin.password.change'));

        // Can view the change password page
        $formResponse = $this->actingAs($staff)->get(route('admin.password.change'));
        $formResponse->assertStatus(200);
        $formResponse->assertSee('Set New Password');

        // Cannot update with mismatched password
        $failResponse = $this->actingAs($staff)->post(route('admin.password.update'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'mismatch',
        ]);
        $failResponse->assertSessionHasErrors('password');

        // Successfully update password
        $successResponse = $this->actingAs($staff)->post(route('admin.password.update'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $successResponse->assertRedirect(route('admin.dashboard'));
        $successResponse->assertSessionHas('success');

        // Verify database state
        $staff->refresh();
        $this->assertFalse((bool)$staff->must_change_password);
        $this->assertTrue(Hash::check('newpassword123', $staff->password));

        // Now can access dashboard freely
        $dashResponse = $this->actingAs($staff)->get('/admin/dashboard');
        $dashResponse->assertStatus(200);
    }

    public function test_super_admin_can_manage_users(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        // 1. Super admin can access user list
        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('System Users');
        $response->assertSee('manager@gurabicha.com');

        // 2. Super admin can create a new staff member
        $createResponse = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Floor Hostess Selam',
            'email' => 'selam@gurabicha.com',
            'role' => 'STAFF',
            'password' => 'tempPass2026!',
            'must_change_password' => '1',
        ]);
        $createResponse->assertRedirect('/admin/users');
        $createResponse->assertSessionHas('success');

        $newUser = User::where('email', 'selam@gurabicha.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('STAFF', $newUser->role);
        $this->assertTrue((bool)$newUser->must_change_password);

        // 3. Super admin can update user & reset password
        $updateResponse = $this->actingAs($admin)->put("/admin/users/{$newUser->id}", [
            'name' => 'Floor Hostess Selam Updated',
            'email' => 'selam@gurabicha.com',
            'role' => 'STAFF',
            'status' => 'ACTIVE',
            'password' => 'BrandNewPass99!',
            'must_change_password' => '1',
        ]);
        $updateResponse->assertRedirect('/admin/users');
        $updateResponse->assertSessionHas('success');
        $this->assertTrue(Hash::check('BrandNewPass99!', $newUser->fresh()->password));

        // 4. Super admin can delete user
        $deleteResponse = $this->actingAs($admin)->delete("/admin/users/{$newUser->id}");
        $deleteResponse->assertRedirect('/admin/users');
        $this->assertNull(User::where('email', 'selam@gurabicha.com')->first());
    }

    public function test_manager_cannot_access_user_management(): void
    {
        $manager = User::where('email', 'manager@gurabicha.com')->first();

        // Manager should get 403 Forbidden on user management route
        $response = $this->actingAs($manager)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_customer_fan_club_profile_api(): void
    {
        // 1. Create/Update fan profile
        $payload = [
            'name' => 'Dawit Haile',
            'phone' => '+251911998877',
            'email' => 'dawit@example.com',
            'favorite_club' => 'Arsenal',
            'notify_matchday' => true,
            'notify_scores' => true,
            'notify_acoustic' => false,
        ];

        $postResponse = $this->postJson('/api/customer/profile', $payload);
        $postResponse->assertStatus(200);
        $postResponse->assertJson([
            'message' => 'Fan Club profile saved successfully!',
            'customer' => [
                'name' => 'Dawit Haile',
                'favorite_club' => 'Arsenal',
                'notify_matchday' => true,
                'notify_scores' => true,
            ],
        ]);

        $this->assertDatabaseHas('customers', [
            'phone' => '+251911998877',
            'favorite_club' => 'Arsenal',
        ]);

        // 2. Retrieve customer profile by phone
        $getResponse = $this->getJson('/api/customer/profile?phone=' . urlencode('+251911998877'));
        $getResponse->assertStatus(200);
        $getResponse->assertJson([
            'customer' => [
                'name' => 'Dawit Haile',
                'favorite_club' => 'Arsenal',
            ],
        ]);

        // 3. Query non-existent phone
        $notFound = $this->getJson('/api/customer/profile?phone=' . urlencode('+251999999999'));
        $notFound->assertStatus(200);
        $notFound->assertJson([
            'customer' => null,
        ]);
    }

    public function test_admin_can_manage_customers_and_broadcast(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        // 1. View customers
        $response = $this->actingAs($admin)->get('/admin/customers');
        $response->assertStatus(200);
        $response->assertSee('Patron Database');
        $response->assertSee('Arsenal');

        // 2. Broadcast alert simulation
        $broadcastResponse = $this->actingAs($admin)->post('/admin/customers/broadcast-alert', [
            'club' => 'Arsenal',
            'type' => 'MATCHDAY',
            'message' => 'Arsenal vs Chelsea kicks off at 8:00 PM! Big screens ready, live acoustic pre-game.',
        ]);

        $broadcastResponse->assertRedirect('/admin/customers');
        $broadcastResponse->assertSessionHas('success');
    }
}
