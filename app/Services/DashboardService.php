<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

class DashboardService
{
    protected $tagService;
    protected $categoryService;

    public function __construct(TagService $tagService, CategoryService $categoryService)
    {
        $this->tagService = $tagService;
        $this->categoryService = $categoryService;
    }

    public function summary($request)
    {
        $limit = $request->input('limit', 10);
        $user = auth()->user();
        $summary = [
            'totalPosts'      => $user->role === 'admin' ? Post::count() : Post::where('user_id', $user->id)->count(),
            'totalCategories' => Category::count(),
            'totalTags'       => Tag::count(),
            'popularTags'        => $this->tagService->getPopularTags($limit),
            'popularCategories'  => $this->categoryService->getPopularCategories($limit),
        ];

        if ($user->role === 'admin') {
            $summary['totalUsers'] = User::count();
        }

        return $summary;
    }
}
