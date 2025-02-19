<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class ForgotPasswordRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'email' => 'required|string|email',
        ];
    }
}
