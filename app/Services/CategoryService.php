<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getAllCategories()
    {
        return Category::orderBy('name', 'asc')->get();
    }

    public function getCategoryById($id)
    {
        return Category::findOrFail($id);
    }

    public function createCategory($data)
    {
        return Category::create([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);
    }

    public function updateCategory($id, $data)
    {
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? $category->parent_id,
        ]);

        return $category;
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        return $category->delete();
    }

    public function getPopularCategories($limit = 10)
    {
        return Category::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
