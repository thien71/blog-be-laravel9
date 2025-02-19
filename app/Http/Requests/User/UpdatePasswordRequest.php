<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class UpdatePasswordRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'new_password' => 'required|min:6|confirmed',
        ];
    }
}
