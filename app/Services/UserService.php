<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAllUsers()
    {
        return User::orderBy('created_at', 'desc');
    }

    public function getUserById($id)
    {
        $user = User::findOrFail($id);
        return $user;
    }

    public function updateUser($user, $data)
    {
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $filename = time() . '.' . $data['avatar']->getClientOriginalExtension();
            $data['avatar']->move(public_path('avatars'), $filename);
            $data['avatar'] = 'avatars/' . $filename;
        }

        $user->update([
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password,
            'avatar' => $data['avatar'] ?? $user->avatar,
        ]);

        return $user;
    }

    public function deleteUser($user)
    {
        return $user->delete();
    }

    public function updateRole($user, $newRole, $admin)
    {
        if ($admin->id === $user->id) {
            return response()->json(['message' => 'You cannot change your own role'], 403);
        }

        if (!in_array($newRole, ['admin', 'author'])) {
            return response()->json(['message' => 'Invalid role'], 400);
        }

        $user->update(['role' => $newRole]);

        return $user;
    }
}
