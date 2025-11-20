<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $productId = $this->route('product'); // null for create, ID for update

        return [
            'product_name' => 'required|string|max:255|unique:products,product_name,' . $productId,
            'description' => 'required|nullable|string',
            'short_description' => 'nullable|string|max:255',
            'product_sku' => 'nullable|string|max:255|unique:products,product_sku,' . $productId,

             'category_ids' => 'nullable|array',
             'category_ids.*' => 'exists:categories,id',
        ];
    }

}
