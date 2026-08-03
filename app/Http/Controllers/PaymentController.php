<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RespondsToModals;
use App\Http\Requests\InstallmentRequest;
use App\Http\Requests\PaymentRequest;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use RespondsToModals;

    public function store(PaymentRequest $request, Patient $patient)
    {
        $this->authorize('create', Payment::class);

        $data = $request->validated();
        $type = $data['payment_type'];
        $total = (float) $data['total_amount'];

        DB::transaction(function () use ($data, $type, $total, $patient, $request) {
            $payment = new Payment([
                'patient_id' => $patient->id,
                'treatment_id' => $data['treatment_id'],
                'appointment_id' => $data['appointment_id'] ?? null,
                'payment_type' => $type,
                'total_amount' => $total,
                'due_date' => $data['due_date'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            // Paid Now is auto-filled and locked to the full cost; Pay
            // Later starts at zero with no payment date yet.
            $payment->amount_paid = match ($type) {
                'paid_now' => $total,
                default => 0,
            };
            $payment->payment_date = $type === 'paid_now' ? now()->toDateString() : null;
            $payment->remaining_balance = $total - $payment->amount_paid;
            $payment->status = $type === 'paid_now' ? Payment::STATUS_PAID : Payment::STATUS_PENDING;
            $payment->save();

            if ($type === 'installment' && ! empty($data['first_installment_amount'])) {
                $payment->installments()->create([
                    'amount' => $data['first_installment_amount'],
                    'paid_date' => now()->toDateString(),
                    'created_by' => $request->user()->id,
                ]);
                // recalculate() fires automatically via the installment's
                // saved-model event.
            }
        });

        return $this->respondSuccess(
            $request,
            __('messages.payment_recorded'),
            'patients.show',
            ['patient' => $patient]
        );
    }

    public function addInstallment(InstallmentRequest $request, Payment $payment)
    {
        $this->authorize('update', $payment);

        $payment->installments()->create([
            'amount' => $request->validated('amount'),
            'paid_date' => $request->validated('paid_date'),
            'created_by' => $request->user()->id,
        ]);

        return $this->respondSuccess(
            $request,
            __('messages.installment_added'),
            'patients.show',
            ['patient' => $payment->patient_id]
        );
    }

    public function destroy(Request $request, Payment $payment)
    {
        $this->authorize('delete', $payment);

        $patientId = $payment->patient_id;
        $payment->delete();

        return $this->respondSuccess($request, __('messages.payment_deleted'), 'patients.show', ['patient' => $patientId]);
    }
}
