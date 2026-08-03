<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppointmentConflictTest extends TestCase
{
    use RefreshDatabase;

    private User $doctorUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => User::ROLE_DOCTOR, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => User::ROLE_RECEPTIONIST, 'guard_name' => 'web']);

        $this->doctorUser = User::factory()->create();
        $this->doctorUser->assignRole(User::ROLE_DOCTOR);
    }

    private function bookAppointment(array $overrides = []): \Illuminate\Testing\TestResponse
    {
        $defaults = [
            'patient_id' => Patient::factory()->create()->id,
            'visit_type' => 'follow_up',
            'appointment_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '10:30',
        ];

        return $this->actingAs($this->doctorUser)->post('/appointments', array_merge($defaults, $overrides));
    }

    public function test_same_doctor_cannot_be_double_booked_for_overlapping_times(): void
    {
        $doctor = Doctor::factory()->create();
        $roomOne = Room::factory()->create();
        $roomTwo = Room::factory()->create();

        $this->bookAppointment(['doctor_id' => $doctor->id, 'room_id' => $roomOne->id])
            ->assertSessionHasNoErrors();

        // Same doctor, different room, overlapping time -> should conflict.
        $response = $this->bookAppointment([
            'doctor_id' => $doctor->id,
            'room_id' => $roomTwo->id,
            'start_time' => '10:15',
            'end_time' => '10:45',
        ]);

        $response->assertSessionHasErrors('appointment_date');
    }

    public function test_same_room_cannot_be_double_booked_for_overlapping_times(): void
    {
        $doctorOne = Doctor::factory()->create();
        $doctorTwo = Doctor::factory()->create();
        $room = Room::factory()->create();

        $this->bookAppointment(['doctor_id' => $doctorOne->id, 'room_id' => $room->id])
            ->assertSessionHasNoErrors();

        // Different doctor, same room, overlapping time -> should conflict.
        $response = $this->bookAppointment([
            'doctor_id' => $doctorTwo->id,
            'room_id' => $room->id,
            'start_time' => '10:15',
            'end_time' => '10:45',
        ]);

        $response->assertSessionHasErrors('appointment_date');
    }

    public function test_non_overlapping_times_for_the_same_doctor_and_room_are_allowed(): void
    {
        $doctor = Doctor::factory()->create();
        $room = Room::factory()->create();

        $this->bookAppointment(['doctor_id' => $doctor->id, 'room_id' => $room->id])
            ->assertSessionHasNoErrors();

        $response = $this->bookAppointment([
            'doctor_id' => $doctor->id,
            'room_id' => $room->id,
            'start_time' => '10:30',
            'end_time' => '11:00',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_cancelled_appointments_do_not_block_new_bookings(): void
    {
        $doctor = Doctor::factory()->create();
        $room = Room::factory()->create();

        $this->bookAppointment(['doctor_id' => $doctor->id, 'room_id' => $room->id])
            ->assertSessionHasNoErrors();

        \App\Models\Appointment::first()->update(['status' => 'cancelled']);

        $response = $this->bookAppointment([
            'doctor_id' => $doctor->id,
            'room_id' => $room->id,
            'start_time' => '10:00',
            'end_time' => '10:30',
        ]);

        $response->assertSessionHasNoErrors();
    }
}
