<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SalesOrderUpdateRequest extends FormRequest
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
            'customer_id' => 'required',
            'order_date' => 'required|date',
            'due_date' => 'nullable|date',
            'payment_status' => 'required|in:paid,due,partial due,cancel',
            'status' => 'required|in:confirmed,cancelled',
            'memo_no' => 'nullable',
            'notes' => 'nullable',
            'products' => 'required',
            'sub_total_amount' => 'required',
            'total_amount' => 'required',
            'paid_amount' => 'nullable',
            'due_amount' => 'nullable',
        ];
    }
    public function messages()
    {
        return [
            'customer_id.required' => 'Customer is required',
            'order_date.required' => 'Order date is required',
            'order_date.date' => 'Order date must be a date',
            'due_date.date' => 'Due date must be a date',
            'payment_status.required' => 'Payment status is required',
            'payment_status.in' => 'Please select valid payment status.',
            'status.required' => 'Status is required.',
            'status.in' => 'Please select valid status.',
            'products.required' => 'Products is required',
            'sub_total_amount.required' => 'Sub total amount is required',
            'total_amount.required' => 'Total amount is required',
        ];
    }
}
