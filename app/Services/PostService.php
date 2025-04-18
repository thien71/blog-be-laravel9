<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;

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

    public function findAuthorizedPost($id)
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return Post::findOrFail($id);
        }

        return Post::where('id', $id)->where('user_id', $user->id)->firstOrFail();
    }

    public function getTwoSentences($text)
    {
        $cleanText = trim(html_entity_decode(strip_tags($text)));
        preg_match_all('/([^.!?]+[.!?]+)/u', $cleanText, $matches);

        if (isset($matches[0]) && count($matches[0]) > 1) {
            // Nếu có đủ 2 câu, ghép 2 câu đầu
            return trim($matches[0][0] . ' ' . $matches[0][1]);
        } elseif (isset($matches[0][0])) {
            // Nếu chỉ có 1 câu thì trả về câu đó
            return trim($matches[0][0]);
        }

        return mb_substr($cleanText, 0, 200);
    }

    public function createPost($data)
    {
        if (isset($data['thumbnail'])) {
            $filename = time() . '.' . $data['thumbnail']->getClientOriginalExtension();
            $data['thumbnail']->move(public_path('thumbnails'), $filename);
            $data['thumbnail'] = 'thumbnails/' . $filename;
        }

        $cleanContent = trim(preg_replace("/[\r\n]+/", " ", $data['content']));

        $post = Post::create([
            'user_id'     => auth()->id(),
            'title'       => $data['title'],
            'content'     => $data['content'],
            'summary'     => $this->getTwoSentences($cleanContent),
            'category_id' => $data['category_id'],
            'slug'        => Post::generateUniqueSlug($data['title']),
            'thumbnail'   => $data['thumbnail'] ?? null,
            'views'       => 0,
            'status'      => auth()->user()->role === 'admin' ? 'published' : 'pending',
        ]);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post;
    }

    public function createDraft($data)
    {
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
            $filename = time() . '.' . $data['thumbnail']->getClientOriginalExtension();
            $data['thumbnail']->move(public_path('thumbnails'), $filename);
            $data['thumbnail'] = 'thumbnails/' . $filename;
        }

        $title = isset($data['title']) && trim($data['title']) !== '' ? $data['title'] : '';
        $content = isset($data['content']) ? $data['content'] : '';
        $categoryId = isset($data['category_id']) ? $data['category_id'] : null;

        $cleanContent = trim(preg_replace("/[\r\n]+/", " ", $content));

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $title,
            'content' => $content,
            'summary'     => $this->getTwoSentences($cleanContent),
            'category_id' => $categoryId,
            'slug' => Post::generateUniqueSlug($data['title']),
            'thumbnail' => $data['thumbnail'] ?? null,
            'views' => 0,
            'status' => 'draft',
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

        $cleanContent = isset($data['content'])
            ? trim(preg_replace("/[\r\n]+/", " ", $data['content']))
            : null;

        $post->update([
            'title' => $data['title'] ?? $post->title,
            'category_id' => $data['category_id'] ?? $post->category_id,
            'content' => $data['content'] ?? $post->content,
            'summary'     => isset($cleanContent) ? $this->getTwoSentences($cleanContent) : $post->summary,
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
        $post = Post::where('user_id', $id)->where('status', 'published')->orderBy('created_at', 'desc');
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
        $user = auth()->user();
        $query = Post::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take($limit);

        if ($user && $user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return $query->get();
    }

    public function getPopularPosts($limit)
    {
        $user = auth()->user();
        $query = Post::where('status', 'published')
            ->orderBy('views', 'desc')
            ->take($limit);

        if ($user && $user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return $query->get();
    }


    public function getRandomPosts($limit)
    {
        return Post::where('status', 'published')
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    public function getRandomPostsByCategory($categoryLimit = 5, $postLimit = 5)
    {
        $categories = Category::has('posts', '>=', $postLimit)
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

    public function getDraftPosts()
    {
        $user = auth()->user();
        return Post::where('user_id', $user->id)->where('status', 'draft')->orderBy('created_at', 'desc');
    }

    public function getPendingPosts()
    {
        if (auth()->user()->role === 'author') {
            return Post::where('status', 'pending')
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc');
        } elseif (auth()->user()->role === 'admin') {
            return Post::where('status', 'pending')
                ->orderBy('created_at', 'desc');
        }

        return collect();
    }

    public function getRejectedPosts()
    {
        if (auth()->user()->role === 'author') {
            return Post::where('status', 'rejected')
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc');
        } elseif (auth()->user()->role === 'admin') {
            return Post::where('status', 'rejected')
                ->orderBy('created_at', 'desc');
        }

        return collect();
    }

    public function getRelatedPosts($request, $postId)
    {
        $limit = $request->input('limit', 5);
        $currentPost = Post::findOrFail($postId);

        if (!$currentPost) {
            return response()->json(['message' => 'Bài viết không tồn tại'], 404);
        }

        // Lấy category và tags từ bài viết gốc
        $categoryId = $currentPost->category_id;
        $tags = $currentPost->tags->pluck('name')->toArray();

        // Truy vấn các bài viết liên quan dựa trên category hoặc tags
        $query = Post::where('id', '!=', $postId)
            ->where('status', 'published')
            ->where(function ($q) use ($categoryId, $tags) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('tags', function ($q2) use ($tags) {
                        $q2->whereIn('name', $tags);
                    });
            });

        // Lấy danh sách bài viết liên quan
        $relatedPosts = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Nếu số lượng bài viết liên quan chưa đủ, bổ sung thêm bài khác
        if ($relatedPosts->count() < $limit) {
            $extraPosts = Post::where('id', '!=', $postId)
                ->where('status', 'published')
                ->whereNotIn('id', $relatedPosts->pluck('id')->toArray())
                ->orderBy('created_at', 'desc')
                ->limit($limit - $relatedPosts->count())
                ->get();

            // Nối thêm bài bổ sung vào danh sách bài liên quan
            $relatedPosts = $relatedPosts->concat($extraPosts);
        }

        return $relatedPosts;
    }

    public function getRelatedTagPosts($request, $id)
    {
        $limit = $request->input('limit', 5);
        $currentPost = Post::with('tags')->find($id);

        if (!$currentPost) {
            return response()->json(['message' => 'Bài viết không tồn tại'], 404);
        }

        $tags = $currentPost->tags->pluck('name')->toArray();

        return Post::where('id', '!=', $id)
            ->where('status', 'published')
            ->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('name', $tags);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRelatedCategoryPosts($request, $id)
    {
        $limit = $request->input('limit', 5);
        $currentPost = Post::find($id);
        if (!$currentPost) {
            return response()->json(['message' => 'Bài viết không tồn tại'], 404);
        }

        return Post::where('id', '!=', $id)
            ->where('status', 'published')
            ->where('category_id', $currentPost->category_id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
