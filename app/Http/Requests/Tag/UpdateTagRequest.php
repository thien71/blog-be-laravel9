<?php

namespace App\Http\Requests\Tag;

use App\Http\Requests\BaseRequest;

class UpdateTagRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
