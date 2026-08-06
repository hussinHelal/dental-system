<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('working_hours')) {
            $this->merge([
                'working_hours' => $this->normalizeWorkingHoursText($this->input('working_hours')),
            ]);
        }
    }

    private function normalizeWorkingHoursText($value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $text = trim($value);
        if ($text === '') {
            return null;
        }

        return $text;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'specialty' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'working_hours' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
