<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class UpdateRoleRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'role' => 'required|in:admin,author',
        ];
    }
}
