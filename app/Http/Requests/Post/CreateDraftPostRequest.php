<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\BaseRequest;

class CreateDraftPostRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'title' => 'nullable|string|max:255',
            'category_id' => 'nullable',
            'content' => 'nullable',
            'thumbnail' => 'nullable',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ];
    }
}
