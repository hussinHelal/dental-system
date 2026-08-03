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
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => [
                'required',
                'string',
                'max:30',
                'regex:/^[0-9+\-\s]{7,30}$/',
                Rule::unique('patients', 'phone')->ignore($patientId),
            ],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'age' => ['nullable', 'integer', 'min:0', 'max:130'],
            'address' => ['nullable', 'string'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
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
