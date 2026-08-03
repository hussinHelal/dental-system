<?php

namespace Tests\Unit;

use App\Models\Patient;
use App\Models\Payment;
use App\Models\Treatment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentRecalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_installments_update_remaining_balance_and_status(): void
    {
        $patient = Patient::factory()->create();
        $treatment = Treatment::factory()->create(['default_cost' => 3000]);

        $payment = Payment::create([
            'patient_id' => $patient->id,
            'treatment_id' => $treatment->id,
            'payment_type' => Payment::TYPE_INSTALLMENT,
            'total_amount' => 3000,
            'amount_paid' => 0,
            'remaining_balance' => 3000,
            'status' => Payment::STATUS_INSTALLMENT,
        ]);

        $payment->installments()->create(['amount' => 1000, 'paid_date' => now()->toDateString()]);
        $payment->refresh();

        $this->assertEquals(1000, $payment->amount_paid);
        $this->assertEquals(2000, $payment->remaining_balance);
        $this->assertEquals(Payment::STATUS_INSTALLMENT, $payment->status);

        $payment->installments()->create(['amount' => 2000, 'paid_date' => now()->toDateString()]);
        $payment->refresh();

        $this->assertEquals(3000, $payment->amount_paid);
        $this->assertEquals(0, $payment->remaining_balance);
        $this->assertEquals(Payment::STATUS_PAID, $payment->status);
    }
}
