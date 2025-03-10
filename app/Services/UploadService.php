<?php

namespace App\Services;

class UploadService
{
    public function uploadImages(array $files): array
    {
        $urls = [];

        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $urls[] = asset('images/' . $filename);
        }

        return $urls;
    }
}
