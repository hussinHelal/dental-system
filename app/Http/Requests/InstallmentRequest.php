<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class InstallmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            /** @var \App\Models\Payment $payment */
            $payment = $this->route('payment');
            $amount = (float) $this->input('amount', 0);

            if ($payment && $amount > (float) $payment->remaining_balance) {
                $validator->errors()->add(
                    'amount',
                    __('messages.installment_exceeds_balance')
                );
            }
        });
    }
}
