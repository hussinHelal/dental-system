<?php

namespace Tests\Unit;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_summary_returns_zeroes_for_patients_without_payments(): void
    {
        $patient = Patient::factory()->create();

        $summary = $patient->paymentSummary();

        $this->assertSame(0.0, $summary['total_cost']);
        $this->assertSame(0.0, $summary['paid']);
        $this->assertSame(0.0, $summary['remaining']);
    }
}
