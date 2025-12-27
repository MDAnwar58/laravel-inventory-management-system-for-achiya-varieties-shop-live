<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StaffUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('staff'); // Assumes route parameter is named 'staff'

        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $userId,
            'phone_number' => 'nullable|string|unique:users,phone_number,' . $userId,
            'salary' => 'nullable',
            'city' => 'nullable|string|max:255',
            'zip_code' => 'nullable|max:20',
            'present_address' => 'required|string',
            'address' => 'nullable|string',
            'role' => 'required|string|in:admin,super_admin,manager,staff', // Adjust roles as needed
            'is_active' => 'sometimes|boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
