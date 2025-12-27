<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileStoreRequest extends FormRequest
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
        $userId = auth()->id();

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone_number')->ignore($userId),
            ],
            'city' => 'nullable|string|max:255',
            'zip_code' => 'required|string|max:20',
            'present_address' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'front_side' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
            'back_side' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
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
            'name.required' => 'The name field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already in use.',
            'phone_number.unique' => 'This phone number is already in use.',
            'zip_code.required' => 'The zip code field is required.',
            'present_address.required' => 'The present address field is required.',
            'front_side.image' => 'The front side must be an image.',
            'front_side.mimes' => 'The front side must be a file of type: jpeg, png, jpg.',
            'front_side.max' => 'The front side may not be greater than 20MB.',
            'back_side.image' => 'The back side must be an image.',
            'back_side.mimes' => 'The back side must be a file of type: jpeg, png, jpg.',
            'back_side.max' => 'The back side may not be greater than 20MB.',
            'avatar.image' => 'The avatar must be an image.',
            'avatar.mimes' => 'The avatar must be a file of type: jpeg, png, jpg.',
            'avatar.max' => 'The avatar may not be greater than 20MB.',
        ];
    }
}