<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\BaseRequest;

class CreatePostRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'required',
            'content' => 'required',
            'thumbnail' => 'required',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ];
    }
}
