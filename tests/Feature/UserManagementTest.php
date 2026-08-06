<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_create_staff_and_continue_to_create_another(): void
    {
        app()['cache']->forget('spatie.permission.cache');
        Role::create(['name' => User::ROLE_DOCTOR, 'guard_name' => 'web']);
        Role::create(['name' => User::ROLE_RECEPTIONIST, 'guard_name' => 'web']);

        $doctor = User::factory()->create([
            'name' => 'Dr. Admin',
            'username' => 'doctor-admin',
        ]);
        $doctor->assignRole(User::ROLE_DOCTOR);

        $response = $this->actingAs($doctor)->post(route('users.store'), [
            'name' => 'New Staff',
            'username' => 'new-staff',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'working_hours' => 'Saturday 1pm-10pm, Sunday 1pm-10pm',
            'create_another' => '1',
        ]);

        $response->assertRedirect(route('users.create'));
        $response->assertSessionHas('success', __('messages.staff_created_and_another'));
        $this->assertDatabaseHas('users', ['username' => 'new-staff']);
    }
}
