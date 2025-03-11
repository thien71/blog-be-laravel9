<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    public function getAllPosts()
    {
        return Post::where('status', 'published')
            ->orderBy('created_at', 'desc');
    }

    public function getPostBySlug($slug)
    {
        $post = Post::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $post->increment('views');
        return $post;
    }

    public function getPostById($id)
    {
        $post = Post::where('id', $id)->firstOrFail();
        return $post;
    }

    public function createPost($data)
    {
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
            $filename = time() . '.' . $data['thumbnail']->getClientOriginalExtension();
            $data['thumbnail']->move(public_path('thumbnails'), $filename);
            $data['thumbnail'] = 'thumbnails/' . $filename;
        }

        $title = isset($data['title']) && trim($data['title']) !== '' ? $data['title'] : '';
        $content = isset($data['content']) ? $data['content'] : '';
        $categoryId = isset($data['category_id']) ? $data['category_id'] : null;

        $isEmpty = $title === '' || $content === '' || $categoryId === null;

        $post = Post::create([
            'user_id'     => auth()->id(),
            'title'       => $title,
            'content'     => $content,
            'category_id' => $categoryId,
            'slug'        => Post::generateUniqueSlug($title) ?? ('default-slug-' . time()),
            'thumbnail'   => $data['thumbnail'] ?? null,
            'views'       => 0,
            'status'      => $isEmpty ? 'draft' : (auth()->user()->role === 'admin' ? 'published' : 'pending'),
        ]);

        if (!empty($data['tags']) && is_array($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post;
    }



    public function findAuthorizedPost($id)
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return Post::findOrFail($id);
        }

        return Post::where('id', $id)->where('user_id', $user->id)->firstOrFail();
    }

    public function updatePost($post, $data)
    {
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
            if ($post->thumbnail && file_exists(public_path($post->thumbnail))) {
                unlink(public_path($post->thumbnail));
            }

            $filename = time() . '.' . $data['thumbnail']->getClientOriginalExtension();
            $data['thumbnail']->move(public_path('thumbnails'), $filename);
            $data['thumbnail'] = 'thumbnails/' . $filename;
        }

        $slug = isset($data['title']) && $data['title'] !== $post->title
            ? Post::generateUniqueSlug($data['title'], $post->id)
            : $post->slug;

        $post->update([
            'title' => $data['title'] ?? $post->title,
            'category_id' => $data['category_id'] ?? $post->category_id,
            // 'summary' => $data['summary'] ?? $post->summary,
            'content' => $data['content'] ?? $post->content,
            'slug' => $slug,
            'thumbnail' => $data['thumbnail'] ?? $post->thumbnail,
            'status' => $data['status'] ?? $post->status,
        ]);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post;
    }

    public function updateDraftPost($post, $data)
    {
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
            if ($post->thumbnail && file_exists(public_path($post->thumbnail))) {
                unlink(public_path($post->thumbnail));
            }

            $filename = time() . '.' . $data['thumbnail']->getClientOriginalExtension();
            $data['thumbnail']->move(public_path('thumbnails'), $filename);
            $data['thumbnail'] = 'thumbnails/' . $filename;
        }

        $slug = isset($data['title']) && $data['title'] !== $post->title
            ? Post::generateUniqueSlug($data['title'], $post->id)
            : $post->slug;

        $post->update([
            'title' => $data['title'] ?? $post->title,
            'category_id' => $data['category_id'] ?? $post->category_id,
            // 'summary' => $data['summary'] ?? $post->summary,
            'content' => $data['content'] ?? $post->content,
            'slug' => $slug,
            'thumbnail' => $data['thumbnail'] ?? $post->thumbnail,
            'status' => "draft",
        ]);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post;
    }

    public function submitPost($post, $data)
    {
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
            if ($post->thumbnail && file_exists(public_path($post->thumbnail))) {
                unlink(public_path($post->thumbnail));
            }

            $filename = time() . '.' . $data['thumbnail']->getClientOriginalExtension();
            $data['thumbnail']->move(public_path('thumbnails'), $filename);
            $data['thumbnail'] = 'thumbnails/' . $filename;
        }

        $slug = isset($data['title']) && $data['title'] !== $post->title
            ? Post::generateUniqueSlug($data['title'], $post->id)
            : $post->slug;

        $post->update([
            'title' => $data['title'] ?? $post->title,
            'category_id' => $data['category_id'] ?? $post->category_id,
            // 'summary' => $data['summary'] ?? $post->summary,
            'content' => $data['content'] ?? $post->content,
            'slug' => $slug,
            'thumbnail' => $data['thumbnail'] ?? $post->thumbnail,
            'status' => auth()->user()->role === 'admin' ? 'published' : 'pending',
        ]);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post;
    }

    public function deletePost($post)
    {
        return $post->delete();
    }

    public function forceDeletePost($post)
    {
        return $post->forceDelete();
    }

    public function approvePost($post)
    {
        $post->update(['status' => 'published']);

        return $post;
    }

    public function rejectPost($post)
    {
        $post->update(['status' => 'rejected']);

        return $post;
    }

    public function getPostsByAuthor($id)
    {
        $post = Post::where('user_id', $id)->where('status', 'published')->firstOrFail();
        return $post;
    }

    public function getPostsByTag($id)
    {
        $posts = Post::whereHas('tags', function ($query) use ($id) {
            $query->where('tags.id', $id);
        })
            ->where('status', 'published')
            ->orderBy('created_at', 'desc');

        return $posts;
    }

    public function getLatestPosts($limit)
    {
        return Post::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function getPopularPosts($limit)
    {
        return Post::where('status', 'published')
            ->orderBy('views', 'desc')
            ->take($limit)
            ->get();
    }

    public function getRandomPosts($limit)
    {
        return Post::where('status', 'published')
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }


    public function getRandomPostsByCategory($categoryLimit = 3, $postLimit = 2)
    {
        $categories = Category::has('posts')
            ->inRandomOrder()
            ->take($categoryLimit)
            ->get();

        $categories->each(function ($category) use ($postLimit) {
            $category->setRelation('posts', $category->posts()
                ->where('status', 'published')
                ->inRandomOrder()
                ->take($postLimit)
                ->get());
        });

        return $categories;
    }

    public function getPendingPosts()
    {
        return Post::where('status', 'pending')
            ->orderBy('created_at', 'desc');
    }

    public function getDraftPosts()
    {
        $user = auth()->user();
        return Post::where('user_id', $user->id)->where('status', 'draft')->orderBy('created_at', 'desc');
    }
}