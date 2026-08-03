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
