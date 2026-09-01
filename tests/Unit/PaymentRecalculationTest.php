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

    public function test_pay_later_uses_installment_tracking_when_installments_are_added(): void
    {
        $patient = Patient::factory()->create();
        $treatment = Treatment::factory()->create(['default_cost' => 3000]);

        $payment = Payment::create([
            'patient_id' => $patient->id,
            'treatment_id' => $treatment->id,
            'payment_type' => Payment::TYPE_PAY_LATER,
            'total_amount' => 3000,
            'amount_paid' => 0,
            'remaining_balance' => 3000,
            'status' => Payment::STATUS_PENDING,
        ]);

        $payment->installments()->create(['amount' => 1500, 'paid_date' => now()->toDateString()]);
        $payment->refresh();

        $this->assertEquals(1500, $payment->amount_paid);
        $this->assertEquals(1500, $payment->remaining_balance);
        $this->assertEquals(Payment::STATUS_INSTALLMENT, $payment->status);
    }

    public function test_mark_as_paid_clears_remaining_balance_in_one_lump_sum(): void
    {
        $patient = Patient::factory()->create();
        $treatment = Treatment::factory()->create(['default_cost' => 3000]);

        $payment = Payment::create([
            'patient_id' => $patient->id,
            'treatment_id' => $treatment->id,
            'payment_type' => Payment::TYPE_PAY_LATER,
            'total_amount' => 3000,
            'amount_paid' => 500,
            'remaining_balance' => 2500,
            'status' => Payment::STATUS_PENDING,
        ]);

        $payment->installments()->create(['amount' => 500, 'paid_date' => now()->toDateString()]);
        $payment->markAsPaid();
        $payment->refresh();

        $this->assertEquals(3000, $payment->amount_paid);
        $this->assertEquals(0, $payment->remaining_balance);
        $this->assertEquals(Payment::STATUS_PAID, $payment->status);
        $this->assertEquals(3000, $payment->installments()->sum('amount'));
    }
}
