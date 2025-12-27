<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BrandUpdateRequest extends FormRequest
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
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'The brand name is required.',
            'name.string' => 'The brand name must be a valid string.',
            'name.max' => 'The brand name may not be greater than 255 characters.',
            'status.required' => 'The status field is required.',
            'image.required' => 'The image field is required.',
            'image.image' => 'The image must be a valid image.',
            'image.mimes' => 'The image must be `jpeg,png,jpg,gif` type.', // jpeg,png,jpg,gif
            'image.max' => 'The image may not be greater than 20MB.',
        ];
    }
}
