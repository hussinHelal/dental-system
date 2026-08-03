<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $doctorA = Doctor::where('name', 'Dr. Amina Zedan')->first();
        $doctorB = Doctor::where('name', 'Dr. Karim Fathy')->first();
        $room1 = Room::where('name', 'Room 1')->first();
        $room2 = Room::where('name', 'Room 2')->first();
        $rootCanal = Treatment::where('name', 'Root Canal')->first();
        $filling = Treatment::where('name', 'Filling')->first();
        $cleaning = Treatment::where('name', 'Cleaning')->first();
        $creator = User::where('username', 'reception')->first();

        $patients = Patient::orderBy('id')->get();
        if ($patients->count() < 8 || ! $doctorA || ! $doctorB || ! $room1 || ! $room2) {
            return;
        }

        $today = Carbon::today();

        $simpleVisits = [
            ['patient' => $patients[0], 'doctor' => $doctorA, 'room' => $room1, 'treatment' => $cleaning, 'day' => 0, 'start' => '09:00', 'end' => '09:45', 'status' => 'completed'],
            ['patient' => $patients[1], 'doctor' => $doctorB, 'room' => $room2, 'treatment' => $filling, 'day' => 0, 'start' => '10:00', 'end' => '10:30', 'status' => 'scheduled'],
            ['patient' => $patients[2], 'doctor' => $doctorA, 'room' => $room1, 'treatment' => $cleaning, 'day' => 1, 'start' => '11:00', 'end' => '11:45', 'status' => 'scheduled'],
            ['patient' => $patients[3], 'doctor' => $doctorB, 'room' => $room2, 'treatment' => null, 'day' => -3, 'start' => '13:00', 'end' => '13:30', 'status' => 'no_show'],
            ['patient' => $patients[4], 'doctor' => $doctorA, 'room' => $room1, 'treatment' => $filling, 'day' => -1, 'start' => '15:00', 'end' => '15:30', 'status' => 'completed'],
        ];

        foreach ($simpleVisits as $visit) {
            Appointment::firstOrCreate([
                'patient_id' => $visit['patient']->id,
                'doctor_id' => $visit['doctor']->id,
                'appointment_date' => $today->copy()->addDays($visit['day'])->toDateString(),
                'start_time' => $visit['start'],
            ], [
                'room_id' => $visit['room']->id,
                'treatment_id' => $visit['treatment']?->id,
                'visit_type' => 'follow_up',
                'end_time' => $visit['end'],
                'status' => $visit['status'],
                'created_by' => $creator?->id,
            ]);
        }

        // Multi-session Root Canal example for one patient: 3 sessions,
        // two already completed and one still scheduled.
        if ($rootCanal) {
            $rootCanalPatient = $patients[5];
            $sessions = [
                ['day' => -14, 'start' => '09:00', 'end' => '10:00', 'status' => 'completed', 'n' => 1],
                ['day' => -7, 'start' => '09:00', 'end' => '10:00', 'status' => 'completed', 'n' => 2],
                ['day' => 2, 'start' => '09:00', 'end' => '10:00', 'status' => 'scheduled', 'n' => 3],
            ];

            foreach ($sessions as $session) {
                Appointment::firstOrCreate([
                    'patient_id' => $rootCanalPatient->id,
                    'treatment_id' => $rootCanal->id,
                    'session_number' => $session['n'],
                ], [
                    'doctor_id' => $doctorA->id,
                    'room_id' => $room1->id,
                    'visit_type' => $session['n'] === 1 ? 'initial_consultation' : 'follow_up',
                    'appointment_date' => $today->copy()->addDays($session['day'])->toDateString(),
                    'start_time' => $session['start'],
                    'end_time' => $session['end'],
                    'status' => $session['status'],
                    'created_by' => $creator?->id,
                ]);
            }
        }
    }
}
