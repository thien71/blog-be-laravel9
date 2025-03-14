<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreateDraftPostRequest;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\GetPostsRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
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

    // public function updateDraft(UpdatePostRequest $request, $id)
    // {
    //     $post = Post::findOrFail($id);;
    //     $updatedDraftPost = $this->postService->updateDraftPost($post, $request->validated());

    //     return new PostResource($updatedDraftPost);
    // }

    // public function submitPost(UpdatePostRequest $request, $id)
    // {
    //     $post = $this->postService->findAuthorizedPost($id);

    //     $submitPost = $this->postService->submitPost($post, $request->validated());

    //     return new PostResource($submitPost);
    // }

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
        $limitCategory = 5;
        $limitPost = $request->input('limit', 5);
        $categories = $this->postService->getRandomPostsByCategory($limitCategory, $limitPost);
        return response()->json($categories);
    }

    public function getPendingPosts(Request $request)
    {
        $posts = $this->postService->getPendingPosts();
        return PostResource::apiPaginate($posts, $request);
    }

    public function getDraftPosts(Request $request)
    {
        $posts = $this->postService->getDraftPosts();
        return PostResource::apiPaginate($posts, $request);
    }
}
