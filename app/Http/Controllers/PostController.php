<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreateDraftPostRequest;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostCategoryResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(Request $request)
    {
        $posts = $this->postService->getAllPosts();
        return PostResource::apiPaginate($posts, $request);
    }

    public function show($slug)
    {
        $post = $this->postService->getPostBySlug($slug);
        return new PostResource($post);
    }

    public function getPostById($id)
    {
        $post = $this->postService->getPostById($id);
        return new PostResource($post);
    }

    public function store(CreatePostRequest $request)
    {
        $post = $this->postService->createPost($request->validated());
        return new PostResource($post);
    }

    public function createDraft(CreateDraftPostRequest $request)
    {
        $post = $this->postService->createDraft($request->validated());
        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $post = $this->postService->findAuthorizedPost($id);

        $updatedPost = $this->postService->updatePost($post, $request->validated());

        return new PostResource($updatedPost);
    }

    public function destroy($id)
    {
        $post = Post::where('id', $id)->firstOrFail();
        $this->postService->deletePost($post);
        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function forceDeletePost($id)
    {
        $post = Post::where('id', $id)->firstOrFail();
        $this->postService->forceDeletePost($post);
        return response()->json(['message' => 'Post force deleted successfully']);
    }

    public function approve($id)
    {
        $post = Post::where('id', $id)->firstOrFail();
        $this->postService->approvePost($post);
        return response()->json(['message' => 'Post approved  successfully']);
    }

    public function reject($id)
    {
        $post = Post::where('id', $id)->firstOrFail();
        $this->postService->rejectPost($post);
        return response()->json(['message' => 'Post rejected successfully']);
    }

    public function getPostsByAuthor(Request $request, $id)
    {
        $posts = $this->postService->getPostsByAuthor($id);

        return PostResource::apiPaginate($posts, $request);
    }

    public function getPostsByTag(Request $request, $id)
    {
        $posts = $this->postService->getPostsByTag($id);

        return PostResource::apiPaginate($posts, $request);
    }

    public function getLatestPosts(Request $request)
    {
        $limit = $request->input('limit', 1);
        $posts = $this->postService->getLatestPosts($limit);
        return PostResource::collection($posts);
    }

    public function getPopularPosts(Request $request)
    {
        $limit = $request->input('limit', 3);
        $posts = $this->postService->getPopularPosts($limit);
        return PostResource::collection($posts);
    }

    public function getRandomPosts(Request $request)
    {
        $limit = $request->input('limit', 9);
        $posts = $this->postService->getRandomPosts($limit);
        return PostResource::collection($posts);
    }

    public function getRandomPostsByCategory(Request $request)
    {
        $limitCategory = $request->input('limit', 5);
        $categories = $this->postService->getRandomPostsByCategory($limitCategory);
        return PostCategoryResource::collection($categories);
    }

    public function getDraftPosts(Request $request)
    {
        $posts = $this->postService->getDraftPosts();
        return PostResource::apiPaginate($posts, $request);
    }

    public function getPendingPosts(Request $request)
    {
        $posts = $this->postService->getPendingPosts();
        return PostResource::apiPaginate($posts, $request);
    }

    public function getRejectedPosts(Request $request)
    {
        $posts = $this->postService->getRejectedPosts();
        return PostResource::apiPaginate($posts, $request);
    }

    public function getRelatedPosts(Request $request, $id)
    {
        $posts = $this->postService->getRelatedPosts($request, $id);
        return PostResource::collection($posts);
    }

    public function getRelatedCategoryPosts(Request $request, $id)
    {
        $posts = $this->postService->getRelatedCategoryPosts($request, $id);
        return PostResource::collection($posts);
    }

    public function getRelatedTagPosts(Request $request, $id)
    {
        $posts = $this->postService->getRelatedTagPosts($request, $id);
        return PostResource::collection($posts);
    }
}
