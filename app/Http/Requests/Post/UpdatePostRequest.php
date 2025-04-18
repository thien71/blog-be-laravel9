<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\BaseRequest;

class UpdatePostRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'title' => 'nullable',
            'category_id' => 'nullable',
            'content' => 'nullable',
            'thumbnail' => 'nullable',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'status' => 'nullable',
        ];
    }
}
