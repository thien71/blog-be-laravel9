<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class CheckCurrentPasswordRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'password' => 'required',
        ];
    }
}
