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

    public function updateProfile($user, $data)
    {
        $domain = "http://127.0.0.1:8000/";

        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {

            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $filename = time() . '.' . $data['avatar']->getClientOriginalExtension();
            $data['avatar']->move(public_path('avatars'), $filename);
            $data['avatar'] = $domain . 'avatars/' . $filename;
        }

        $user->update([
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'avatar' => $data['avatar'] ?? $user->avatar,
        ]);

        return $user;
    }

    public function checkCurrentPassword($user, $data)
    {
        if (!password_verify($data['password'], $user->password)) {
            return response()->json(['message' => 'The current password is incorrect!'], 400);
        }

        return response()->json(['message' => 'The current password is correct!'], 200);;
    }


    public function updatePassword($user, $data)
    {
        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return $user;
    }

    public function deleteUser($user)
    {
        return $user->delete();
    }
}
