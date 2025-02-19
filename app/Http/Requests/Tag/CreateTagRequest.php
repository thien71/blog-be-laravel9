<?php

namespace App\Http\Requests\Tag;

use App\Http\Requests\BaseRequest;

class CreateTagRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
