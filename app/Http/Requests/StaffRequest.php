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
        if (! is_array($this->input('working_hours'))) {
            return;
        }

        $this->merge([
            'working_hours' => collect($this->input('working_hours'))
                ->map(fn ($value) => $this->normalizeWorkingHoursEntry($value))
                ->all(),
        ]);
    }

    private function normalizeWorkingHoursEntry(?string $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $parts = array_map('trim', explode('-', strtolower($value), 2));
        if (count($parts) !== 2) {
            return $value;
        }

        $start = $this->normalizeWorkingHoursTime($parts[0]);
        $end = $this->normalizeWorkingHoursTime($parts[1]);

        if (! $start || ! $end) {
            return $value;
        }

        return sprintf('%s-%s', $start, $end);
    }

    private function normalizeWorkingHoursTime(string $value): ?string
    {
        $value = trim($value);

        if (preg_match('/^(\d{1,2})(?::(\d{2}))?\s*(am|pm)$/i', $value, $matches)
            || preg_match('/^(am|pm)\s*(\d{1,2})(?::(\d{2}))?$/i', $value, $matches)
        ) {
            if (strtolower($matches[1]) === 'am' || strtolower($matches[1]) === 'pm') {
                $meridiem = strtolower($matches[1]);
                $hour = (int) $matches[2];
                $minute = isset($matches[3]) ? (int) $matches[3] : 0;
            } else {
                $hour = (int) $matches[1];
                $minute = isset($matches[2]) ? (int) $matches[2] : 0;
                $meridiem = strtolower($matches[3]);
            }

            if ($hour < 1 || $hour > 12 || $minute < 0 || $minute > 59) {
                return null;
            }

            if ($meridiem === 'am' && $hour === 12) {
                $hour = 0;
            } elseif ($meridiem === 'pm' && $hour !== 12) {
                $hour += 12;
            }

            return str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $minute, 2, '0', STR_PAD_LEFT);
        }

        if (preg_match('/^(\d{1,2})(?::(\d{2}))?$/', $value, $matches)) {
            $hour = (int) $matches[1];
            $minute = isset($matches[2]) ? (int) $matches[2] : 0;

            if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
                return null;
            }

            return str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $minute, 2, '0', STR_PAD_LEFT);
        }

        return null;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($userId)],
            'password' => [$userId ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'working_hours' => ['nullable', 'array'],
            'working_hours.*' => ['nullable', 'regex:/^\d{2}:\d{2}-\d{2}:\d{2}$/'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
