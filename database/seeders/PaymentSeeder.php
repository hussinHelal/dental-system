<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('username', 'doctor')->first();
        $filling = Treatment::where('name', 'Filling')->first();
        $cleaning = Treatment::where('name', 'Cleaning')->first();
        $rootCanal = Treatment::where('name', 'Root Canal')->first();

        $patients = Patient::orderBy('id')->get();
        if ($patients->count() < 8 || ! $filling || ! $cleaning || ! $rootCanal) {
            return;
        }

        // 1) Paid in full at time of visit.
        $paidNow = Payment::firstOrCreate([
            'patient_id' => $patients[0]->id,
            'treatment_id' => $cleaning->id,
        ], [
            'payment_type' => Payment::TYPE_PAID_NOW,
            'total_amount' => $cleaning->default_cost,
            'amount_paid' => $cleaning->default_cost,
            'remaining_balance' => 0,
            'payment_date' => now()->subDays(0)->toDateString(),
            'status' => Payment::STATUS_PAID,
            'created_by' => $creator?->id,
        ]);

        // 2) Pay later - still pending.
        Payment::firstOrCreate([
            'patient_id' => $patients[1]->id,
            'treatment_id' => $filling->id,
        ], [
            'payment_type' => Payment::TYPE_PAY_LATER,
            'total_amount' => $filling->default_cost,
            'amount_paid' => 0,
            'remaining_balance' => $filling->default_cost,
            'payment_date' => null,
            'due_date' => now()->addDays(14)->toDateString(),
            'status' => Payment::STATUS_PENDING,
            'created_by' => $creator?->id,
        ]);

        // 3) Root canal on an installment plan, in progress.
        $rootCanalAppointment = Appointment::where('treatment_id', $rootCanal->id)
            ->where('patient_id', $patients[5]->id)
            ->orderBy('session_number')
            ->first();

        $installmentPayment = Payment::firstOrCreate([
            'patient_id' => $patients[5]->id,
            'treatment_id' => $rootCanal->id,
        ], [
            'appointment_id' => $rootCanalAppointment?->id,
            'payment_type' => Payment::TYPE_INSTALLMENT,
            'total_amount' => $rootCanal->default_cost,
            'amount_paid' => 0,
            'remaining_balance' => $rootCanal->default_cost,
            'payment_date' => null,
            'status' => Payment::STATUS_INSTALLMENT,
            'created_by' => $creator?->id,
        ]);

        if ($installmentPayment->installments()->count() === 0) {
            $installmentPayment->installments()->create([
                'amount' => 1500,
                'paid_date' => now()->subDays(14)->toDateString(),
                'created_by' => $creator?->id,
            ]);
            $installmentPayment->installments()->create([
                'amount' => 1000,
                'paid_date' => now()->subDays(7)->toDateString(),
                'created_by' => $creator?->id,
            ]);
            // recalculate() runs automatically after each installment
            // save, updating amount_paid/remaining_balance/status.
        }
    }
}
