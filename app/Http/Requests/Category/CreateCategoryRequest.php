<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseRequest;

class CreateCategoryRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
        ];
    }
}
