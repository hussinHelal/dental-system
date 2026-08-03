<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'treatment_id' => ['nullable', 'exists:treatments,id'],
            'session_number' => ['nullable', 'integer', 'min:1', 'max:50'],
            'visit_type' => ['required', Rule::in(['initial_consultation', 'follow_up'])],
            'appointment_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['sometimes', Rule::in([
                'scheduled', 'in_progress', 'completed', 'cancelled', 'no_show',
            ])],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_time' => $this->normalizeTime($this->input('start_time')),
            'end_time' => $this->normalizeTime($this->input('end_time')),
        ]);
    }

    private function normalizeTime(?string $value): ?string
    {
        if (! is_string($value)) {
            return $value;
        }

        $text = strtolower(trim($value));
        $text = preg_replace('/\s+/', '', $text);

        if (preg_match('/^(\d{1,2})$/', $text, $matches)) {
            $hour = intval($matches[1]);
            if ($hour === 0) {
                return '00:00';
            }
            if ($hour >= 1 && $hour <= 11) {
                return sprintf('%02d:00', $hour + 12);
            }
            if ($hour >= 12 && $hour <= 23) {
                return sprintf('%02d:00', $hour);
            }
            return $value;
        }

        if (preg_match('/^(\d{1,2}):(\d{2})$/', $text, $matches)) {
            $hour = intval($matches[1]);
            $minute = intval($matches[2]);
            if ($minute < 0 || $minute > 59) {
                return $value;
            }
            if ($hour === 0) {
                return sprintf('00:%02d', $minute);
            }
            if ($hour >= 1 && $hour <= 11) {
                return sprintf('%02d:%02d', $hour + 12, $minute);
            }
            if ($hour >= 12 && $hour <= 23) {
                return sprintf('%02d:%02d', $hour, $minute);
            }
            return $value;
        }

        if (preg_match('/^(am|pm)(\d{1,2})(?::(\d{2}))?$/', $text, $matches)) {
            $meridiem = $matches[1];
            $hour = intval($matches[2]);
            $minute = isset($matches[3]) ? intval($matches[3]) : 0;
            if ($hour < 1 || $hour > 12 || $minute < 0 || $minute > 59) {
                return $value;
            }
            if ($meridiem === 'pm' && $hour !== 12) {
                $hour += 12;
            }
            if ($meridiem === 'am' && $hour === 12) {
                $hour = 0;
            }
            return sprintf('%02d:%02d', $hour, $minute);
        }

        if (preg_match('/^(\d{1,2})(?::(\d{2}))?(am|pm)$/', $text, $matches)) {
            $hour = intval($matches[1]);
            $minute = isset($matches[2]) ? intval($matches[2]) : 0;
            $meridiem = $matches[3];
            if ($hour < 1 || $hour > 12 || $minute < 0 || $minute > 59) {
                return $value;
            }
            if ($meridiem === 'pm' && $hour !== 12) {
                $hour += 12;
            }
            if ($meridiem === 'am' && $hour === 12) {
                $hour = 0;
            }
            return sprintf('%02d:%02d', $hour, $minute);
        }

        return $value;
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $conflict = Appointment::findConflict(
                doctorId: (int) $this->input('doctor_id'),
                roomId: (int) $this->input('room_id'),
                date: $this->input('appointment_date'),
                startTime: $this->input('start_time'),
                endTime: $this->input('end_time'),
                ignoreId: $this->route('appointment')?->id,
            );

            if ($conflict) {
                $validator->errors()->add(
                    'appointment_date',
                    __('messages.appointment_conflict', [
                        'patient' => $conflict->patient->full_name,
                        'doctor' => $conflict->doctor->name,
                        'room' => $conflict->room->name,
                        'start' => $conflict->start_time,
                        'end' => $conflict->end_time,
                    ])
                );
            }
        });
    }
}
