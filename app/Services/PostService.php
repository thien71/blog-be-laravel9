<?php

namespace App\Services;

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

    public function createPost($data)
    {
        if (isset($data['thumbnail'])) {
            $filename = time() . '.' . $data['thumbnail']->getClientOriginalExtension();

            $data['thumbnail']->move(public_path('thumbnails'), $filename);

            $data['thumbnail'] = 'thumbnails/' . $filename;
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'content' => $data['content'],
            'summary' => $data['summary'],
            'slug' => Post::generateUniqueSlug($data['title']),
            'thumbnail' => $data['thumbnail'] ?? null,
            'views' => 0,
            'status' => auth()->user()->role === 'admin' ? 'published' : 'draft',
        ]);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post;
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
            'summary' => $data['summary'] ?? $post->summary,
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

    public function deletePost($post)
    {
        return $post->delete();
    }

    public function approvePost($post)
    {
        $post->update(['status' => 'published']);

        return $post;
    }
}
