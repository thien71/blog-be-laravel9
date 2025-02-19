<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CheckCurrentPasswordRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateRoleRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers();
        return UserResource::apiPaginate($users, $request);
    }

    public function show($id)
    {
        $user = $this->userService->getUserById($id);
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->id != $id) {
            return response()->json(['message' => 'You are not permission'], 403);
        }

        $user = User::findOrFail($id);
        $updatesUser = $this->userService->updateProfile($user, $request->validated());

        return new UserResource($updatesUser);
    }

    public function checkCurrentPassword(CheckCurrentPasswordRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $checkPassword = $this->userService->checkCurrentPassword($user, $request->validated());

        return $checkPassword;
    }


    public function updatePassword(UpdatePasswordRequest $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->id != $id) {
            return response()->json(['message' => 'You are not permission'], 403);
        }

        $user = User::findOrFail($id);
        $updatesUser = $this->userService->updatePassword($user, $request->validated());

        return new UserResource($updatesUser);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->userService->deleteUser($user);
        return response()->json(['message' => 'User deleted successfully']);
    }
}
