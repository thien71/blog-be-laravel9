<?php

namespace App\Http\Controllers;

use App\Http\Requests\Upload\UploadImageRequest;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function uploadImages(UploadImageRequest $request): JsonResponse
    {
        $urls = $this->uploadService->uploadImages($request->file('images'));

        return response()->json(['urls' => $urls]);
    }
}
