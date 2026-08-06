<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'current_password' => ['required_with:password',  'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
