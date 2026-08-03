<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('username', 'reception')->first();

        $patients = [
            ['full_name' => 'Mona Adel', 'phone' => '01011112221', 'date_of_birth' => '1990-04-12', 'gender' => 'female', 'address' => 'Tanta, Gharbia'],
            ['full_name' => 'Youssef Samir', 'phone' => '01011112222', 'date_of_birth' => '1985-09-03', 'gender' => 'male', 'address' => 'Tanta, Gharbia'],
            ['full_name' => 'Nour El-Din Hesham', 'phone' => '01011112223', 'age' => 34, 'gender' => 'male', 'address' => 'Mahalla, Gharbia'],
            ['full_name' => 'Salma Tarek', 'phone' => '01011112224', 'date_of_birth' => '2001-01-20', 'gender' => 'female', 'address' => 'Tanta, Gharbia'],
            ['full_name' => 'Ahmed Mostafa', 'phone' => '01011112225', 'date_of_birth' => '1978-11-30', 'gender' => 'male', 'address' => 'Kafr El-Sheikh'],
            ['full_name' => 'Farida Nabil', 'phone' => '01011112226', 'age' => 8, 'gender' => 'female', 'address' => 'Tanta, Gharbia', 'notes' => 'Nervous with dental tools, needs a calm approach.'],
            ['full_name' => 'Omar Khaled', 'phone' => '01011112227', 'date_of_birth' => '1995-06-18', 'gender' => 'male', 'address' => 'Zefta, Gharbia'],
            ['full_name' => 'Hana Sameh', 'phone' => '01011112228', 'date_of_birth' => '1988-02-25', 'gender' => 'female', 'address' => 'Tanta, Gharbia'],
            ['full_name' => 'Mahmoud Reda', 'phone' => '01011112229', 'age' => 45, 'gender' => 'male', 'address' => 'Tanta, Gharbia', 'notes' => 'Allergic to penicillin.'],
            ['full_name' => 'Dina Wael', 'phone' => '01011112230', 'date_of_birth' => '1999-12-05', 'gender' => 'female', 'address' => 'Basyoun, Gharbia'],
        ];

        foreach ($patients as $patient) {
            Patient::firstOrCreate(
                ['phone' => $patient['phone']],
                $patient + ['created_by' => $creator?->id]
            );
        }
    }
}
