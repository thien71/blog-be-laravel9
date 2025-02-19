<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TagService
{
    public function getAllTags()
    {
        return Tag::orderBy('created_at', 'desc');
    }

    public function getTagById($id)
    {
        $tag = Tag::where('id', $id)->firstOrFail();
        return $tag;
    }

    public function createTag($data)
    {
        $tag = Tag::create([
            'name' => $data['name'],
        ]);

        return $tag;
    }

    public function updateTag($tag, $data)
    {
        $tag->update([
            'name' => $data['name'],
        ]);

        return $tag;
    }

    public function deleteTag($tag)
    {
        return $tag->delete();
    }
}
