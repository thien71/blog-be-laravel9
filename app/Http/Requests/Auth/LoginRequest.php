<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string:min:8',
        ];
    }
}
