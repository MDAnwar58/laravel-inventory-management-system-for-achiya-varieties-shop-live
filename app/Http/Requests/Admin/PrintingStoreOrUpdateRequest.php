<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PrintingStoreOrUpdateRequest extends FormRequest
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
            'phone_number' => [
                'required',
                'regex:/^[0-9০-৯\-\+]{10,15}$/u',
            ],
            'phone_number2' => [
                'nullable',
                'regex:/^[0-9০-৯\-\+]{10,15}$/u',
            ],
            'location' => 'required|string',
            'short_desc' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.required' => 'Phone Number is required',
            'phone_number.regex' => 'Phone Number must be 10–15 digits (English or Bangla)',
            'phone_number2.regex' => 'Second Phone Number must be 10–15 digits (English or Bangla)',
            // 'location.required' => 'Location is required',
            // 'short_desc.required' => 'Short Description is required',
        ];
    }

}
