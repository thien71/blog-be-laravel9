<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class UpdateUserRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable',
            'avatar' => 'nullable',
        ];
    }
}