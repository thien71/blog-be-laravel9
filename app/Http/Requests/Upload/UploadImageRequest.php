<?php

namespace App\Http\Requests\Upload;

use App\Http\Requests\BaseRequest;

class UploadImageRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'images'   => 'required|array',  // Phải là mảng
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Mỗi phần tử là ảnh
        ];
    }
}
