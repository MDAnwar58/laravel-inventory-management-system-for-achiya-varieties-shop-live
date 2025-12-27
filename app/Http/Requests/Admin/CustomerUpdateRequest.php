<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Update this with your authorization logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customerId = $this->route('customer');

        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|min:10|max:15|unique:customers,phone,' . $customerId,
            'address' => 'nullable|string|max:700'
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.max' => 'Name must be at most 255 characters',
            'phone.required' => 'Phone is required',
            'phone.min' => 'Phone must be at least 10 characters',
            'phone.max' => 'Phone must be at most 15 characters',
            'address.required' => 'Address is required',
            'address.max' => 'Address must be at most 700 characters',
        ];
    }
}
