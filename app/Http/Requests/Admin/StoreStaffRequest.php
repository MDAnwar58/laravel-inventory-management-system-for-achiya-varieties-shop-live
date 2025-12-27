<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Update this based on your authorization logic
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
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|unique:users,phone_number',
            'salary' => 'nullable',
            'city' => 'nullable|string|max:255',
            'zip_code' => 'nullable|max:20',
            'present_address' => 'required|string',
            'address' => 'nullable|string',
            'role' => 'required|string|in:admin,super_admin,manager,staff',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The staff name is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already in use.',
            'phone_number.required' => 'The phone number is required.',
            'phone_number.unique' => 'This phone number is already in use.',
            'zip_code.required' => 'The ZIP code is required.',
            'present_address.required' => 'The present address is required.',
            'role.required' => 'Please select a role for the staff member.',
            'role.in' => 'Please select a valid role.',
            'avatar.image' => 'The uploaded file must be an image.',
            'avatar.mimes' => 'The avatar must be a file of type: jpeg, png, jpg.',
            'avatar.max' => 'The avatar may not be greater than 20MB in size.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'staff name',
            'email' => 'email address',
            'phone_number' => 'phone number',
            'zip_code' => 'ZIP code',
            'present_address' => 'present address',
        ];
    }
}
