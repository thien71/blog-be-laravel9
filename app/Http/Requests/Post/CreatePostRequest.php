<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\BaseRequest;

class CreatePostRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'summary' => 'nullable',
            'content' => 'required',
            'thumbnail' => 'nullable',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ];
    }
}
