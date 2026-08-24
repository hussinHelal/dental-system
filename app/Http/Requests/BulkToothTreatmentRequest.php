<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkToothTreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage tooth chart') ?? false;
    }

    public function rules(): array
    {
        return [
            // The whole point of this endpoint: many teeth, one request.
            'tooth_ids' => ['required', 'array', 'min:1', 'max:32'],
            'tooth_ids.*' => ['integer', 'distinct', 'exists:teeth,id'],

            'treatment_type' => ['required', 'string', Rule::in($this->allowedTreatmentTypes())],

            // Free-text note applies to every tooth in the batch, e.g.
            // "pre-op consult 2026-08-23" — optional, so a quick bulk
            // apply isn't blocked on typing something.
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function allowedTreatmentTypes(): array
    {
        // Kept centralized here rather than hardcoded in the controller so
        // adding a new bulk-eligible treatment type is a one-line change.
        return [
            'veneer',
            'orthodontics',
            'whitening',
            'sealant',
            'fluoride_treatment',
            'cleaning',
        ];
    }
}
