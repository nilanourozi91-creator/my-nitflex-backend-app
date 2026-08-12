<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class prodectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
               'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'brand' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'origin' => ['nullable', 'string', 'max:255'],
            'ingredients' => ['nullable', 'string'],
            'nutrition' => ['nullable', 'string'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'low_stock_limit' => ['nullable', 'integer', 'min:0'],
            'img1'=>'nullable','image|mimes:jpg,jpag,png,gif,webp',
            'img2'=>'nullable','image|mimes:jpg,jpag,png,gif,webp',
        ];
    }
}
