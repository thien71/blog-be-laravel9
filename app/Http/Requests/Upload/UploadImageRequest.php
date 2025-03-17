<?php

namespace App\Http\Requests\Upload;

use App\Http\Requests\BaseRequest;

class UploadImageRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'images'   => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
        ];
    }
}