<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Log;

class UpdateUserRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'nullable',
            'email' => 'nullable',
            'avatar' => 'nullable',
        ];
    }
}
