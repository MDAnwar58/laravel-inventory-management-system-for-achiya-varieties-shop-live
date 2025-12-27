<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Product;

class ProductUpdateRequest extends FormRequest
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
        $productId = $this->route('product');

        return [
            'item_type_id' => ['nullable', 'exists:item_types,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'barcode')->ignore($productId)
            ],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'min:0'],
            'discount_price' => ['nullable', 'min:0', 'lt:price'],
            'retail_price' => ['nullable', 'min:0'],
            'cost_price' => ['required', 'min:0', 'lte:price', 'lte:retail_price'],
            'stock' => ['nullable', 'integer'],
            'stock_w' => ['nullable', 'min:0'],
            'stock_w_type' => ['nullable', 'in:none,kg,ft,yard,m'],
            'low_stock_level' => ['nullable', 'integer'],
            'purchase_limit' => ['nullable', 'integer'],
            'desc' => ['nullable'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'status' => ['required', 'in:active,deactive'],
            // 'meta_title' => ['nullable', 'string', 'max:255'],
            // 'meta_desc' => ['nullable'],
            // 'meta_keywords' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'item_type_id.exists' => 'The selected item type is invalid.',
            'brand_id.exists' => 'The selected brand is invalid.',
            'category_id.exists' => 'The selected category is invalid.',
            'sub_category_id.exists' => 'The selected sub category is invalid.',
            'barcode.unique' => 'This barcode is already in use.',
            'name.required' => 'The product name is required.',
            'price.min' => 'The price must be at least upto 0.',
            // 'discount_price.numeric' => 'The discount price must be a number.',
            'discount_price.min' => 'The discount price must be at least 0.',
            'discount_price.lt' => 'The discount price must be less than the regular price.',
            'cost_price.required' => 'The cost price is required.',
            'cost_price.min' => 'The cost price must be at least 0.',
            'cost_price.lte' => 'The cost price must not be greater than the selling price or retail price.',
            'stock.required' => 'The stock field is required.',
            'stock.integer' => 'The stock must be an integer.',
            'stock.min' => 'The stock cannot be negative.',
            'low_stock_level.required' => 'The low stock level is required.',
            'low_stock_level.integer' => 'The low stock level must be an integer.',
            'purchase_limit.required' => 'The purchase limit is required.',
            'purchase_limit.integer' => 'The purchase limit must be an integer.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image must not be larger than 2MB.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
