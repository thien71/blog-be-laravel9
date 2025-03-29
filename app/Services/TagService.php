<?php

namespace App\Services;

use App\Models\Tag;

class TagService
{
    public function getAllTags()
    {
        return Tag::orderBy('name', 'asc')->get();
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

    public function getPopularTags($limit = 10)
    {
        return Tag::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
