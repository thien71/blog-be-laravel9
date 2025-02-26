<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Posts and Auth
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/latest', [PostController::class, 'getLatestPosts']);
Route::get('/posts/popular', [PostController::class, 'getPopularPosts']);
Route::get('/posts/random', [PostController::class, 'getRandomPosts']);
Route::get('/posts/random-by-category', [PostController::class, 'getRandomPostsByCategory']);
Route::get('/posts/{slug}', [PostController::class, 'show']);
Route::get('/posts/tag/{id}', [PostController::class, 'getPostsByTag']);
Route::get('/posts/author/{id}', [PostController::class, 'getPostsByAuthor']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/admin/register', [AuthController::class, 'register']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::put('/posts/{id}/approve', [PostController::class, 'approve']);
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
});
