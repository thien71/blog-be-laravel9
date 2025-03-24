<?php

namespace App\Services;

use App\Models\Post;

class SearchService
{
    public function search($request)
    {
        $query = $request->input('q');
        $category = $request->input('category');
        $tag = $request->input('tag');

        $posts = Post::query();

        if ($query) {
            $posts->where('title', 'like', "%{$query}%");
        }

        if ($category) {
            if (is_numeric($category)) {
                $posts->where('category_id', $category);
            } else {
                $posts->whereHas('category', function ($q) use ($category) {
                    $q->where('name', 'like', "%{$category}%");
                });
            }
        }

        if ($tag) {
            if (is_numeric($tag)) {
                $posts->whereHas('tags', function ($q) use ($tag) {
                    $q->where('id', $tag);
                });
            } else {
                $posts->whereHas('tags', function ($q) use ($tag) {
                    $q->where('name', 'like', "%{$tag}%");
                });
            }
        }

        // Truyền tham số sort = "latest" hoặc "popular"
        $sort = $request->input('sort');
        if ($sort === 'latest') {
            $posts->orderBy('created_at', 'desc');
        } elseif ($sort === 'popular') {
            $posts->orderBy('views', 'desc');
        }

        return $posts;
    }
}
