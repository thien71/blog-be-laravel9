<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseRequest;

class UpdateCategoryRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->route('category'),
            'parent_id' => 'nullable|exists:categories,id',
        ];
    }
}
