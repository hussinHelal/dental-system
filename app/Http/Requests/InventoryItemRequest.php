<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:0'],
            'unit' => ['required', Rule::in(['box', 'piece', 'ml'])],
            'category' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
        ];
    }
}
