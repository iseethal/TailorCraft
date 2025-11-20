<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

  public function rules()
    {
        $categoryId = $this->route('category');

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $categoryId,
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'nullable|in:0,1',
            'description' => 'nullable|string|max:1000',
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'slug.unique' => 'Slug must be unique.',
            'status.in' => 'Status must be either Active (1) or Inactive (0).',
        ];
    }
}
