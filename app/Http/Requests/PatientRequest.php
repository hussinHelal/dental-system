<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $patientId = $this->route('patient')?->id;

        return [
            'full_name'      => ['required', 'string', 'max:150'],
            'phone'          => [
                'required', 'string', 'max:30',
                /* BUG FIX: require at least one digit */
                'regex:/^(?=.*\d)[0-9+\-\s]{7,30}$/',
                Rule::unique('patients', 'phone')->ignore($patientId),
            ],
            /* BUG FIX: allow babies born today */
            'date_of_birth'  => ['nullable', 'date', 'before_or_equal:today'],
            'age'            => ['nullable', 'integer', 'min:0', 'max:130'],
            'address'        => ['nullable', 'string'],
            'gender'         => ['nullable', Rule::in(['male', 'female'])],
            'notes'          => ['nullable', 'string'],
            'photo'          => ['nullable', 'image', 'max:2048'],
            'xray_photo'     => ['nullable', 'image', 'max:5120'],
            'tooth_chart'    => ['nullable', 'array'],
            'tooth_chart.*'  => ['nullable', 'in:healthy,decayed,treated,missing,root_canal,crown'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => __('messages.patient_phone_duplicate'),
            'phone.regex' => __('messages.patient_phone_invalid'),
        ];
    }
}
