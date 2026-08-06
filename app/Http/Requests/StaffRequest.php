<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
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
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($userId)],
            'password' => [$userId ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'working_hours' => ['nullable', 'string', 'max:500'],
            'role' => ['nullable', 'string', 'in:Doctor,Receptionist'],
            'create_another' => ['nullable', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
