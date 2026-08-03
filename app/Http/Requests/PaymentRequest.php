<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'treatment_id' => ['required', 'exists:treatments,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'payment_type' => ['required', Rule::in(['paid_now', 'pay_later', 'installment'])],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'first_installment_amount' => ['nullable', 'numeric', 'min:0.01'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $type = $this->input('payment_type');
            $total = (float) $this->input('total_amount', 0);
            $first = $this->input('first_installment_amount');

            if ($type === 'installment') {
                if ($first === null) {
                    $validator->errors()->add(
                        'first_installment_amount',
                        __('messages.installment_amount_required')
                    );
                } elseif ((float) $first <= 0 || (float) $first >= $total) {
                    $validator->errors()->add(
                        'first_installment_amount',
                        __('messages.installment_amount_range')
                    );
                }
            }
        });
    }
}
