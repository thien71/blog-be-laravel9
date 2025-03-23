<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Posts and Auth
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/latest', [PostController::class, 'getLatestPosts']);
Route::get('/posts/popular', [PostController::class, 'getPopularPosts']);
Route::get('/posts/random', [PostController::class, 'getRandomPosts']);
Route::get('/posts/random-by-category', [PostController::class, 'getRandomPostsByCategory']);
Route::get('/posts/{id}/related', [PostController::class, 'getRelatedPosts']);
Route::get('/posts/{id}/related/category', [PostController::class, 'getRelatedCategoryPosts']);
Route::get('/posts/{id}/related/tag', [PostController::class, 'getRelatedTagPosts']);
Route::get('/posts/{slug}', [PostController::class, 'show']);
Route::get('/posts/tag/{id}', [PostController::class, 'getPostsByTag']);
Route::get('/posts/author/{id}', [PostController::class, 'getPostsByAuthor']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/posts/draft', [PostController::class, 'createDraft']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/draft/me', [PostController::class, 'getDraftPosts']);
    Route::get('/posts/id/{id}', [PostController::class, 'getPostById']);
    Route::get('/admin/posts/pending', [PostController::class, 'getPendingPosts']);
    Route::get('/admin/posts/reject', [PostController::class, 'getRejectedPosts']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}/force', [PostController::class, 'forceDeletePost']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/admin/register', [AuthController::class, 'register']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::put('/posts/{id}/approve', [PostController::class, 'approve']);
    Route::put('/posts/{id}/reject', [PostController::class, 'reject']);
});

Route::post('/login', [AuthController::class, 'login']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

// Tags
Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/{id}', [TagController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{id}', [TagController::class, 'update']);
    Route::delete('/tags/{id}', [TagController::class, 'destroy']);
});

// Users
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/me', [UserController::class, 'me']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::post('/users/{id}/password/check', [UserController::class, 'checkCurrentPassword']);
    Route::put('/users/{id}/password/update', [UserController::class, 'updatePassword']);
});
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::patch('/users/{id}/restore', [UserController::class, 'restore']);
});

// Upload image
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload-image', [UploadController::class, 'uploadImages']);
});

// Dashboard
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard-summary', [DashboardController::class, 'summary']);
});
