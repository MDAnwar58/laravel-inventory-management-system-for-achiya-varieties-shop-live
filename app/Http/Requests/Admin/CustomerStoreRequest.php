<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|min:10|max:15',
            'address' => 'nullable|string|max:700',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.min' => 'Name must be at least 10 characters',
            'name.max' => 'Name must be at most 255 characters',
            'phone.required' => 'Phone is required',
            'phone.max' => 'Phone must be at most 15 characters',
            'address.max' => 'Address must be at most 700 characters',
        ];
    }
}
