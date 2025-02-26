<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function register(array $data)
    {
        if ($this->user->where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Email đã tồn tại. Vui lòng đăng nhập.'],
            ]);
        }

        // $data['password'] = bcrypt($data['password']);
        $data['password'] = Hash::make($data['password']);

        return $this->user->create($data);
    }

    public function login(array $data)
    {
        $user = $this->user->where('email', $data['email'])->first();

        if (!$user || !password_verify($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'message' => ['Email or Password is incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ];
    }



    public function forgotPassword(string $email): void
    {
        Password::sendResetLink(['email' => $email]);
    }

    public function resetPassword(array $data): void
    {
        $user = User::where('email', $data['email'])->firstOrFail();
        $user->update(['password' => Hash::make($data['password'])]);
    }

    public function logout($token)
    {
        if ($token) {
            $token->delete();
        } else {
            throw new \Exception("Token is null");
        }
    }
}
