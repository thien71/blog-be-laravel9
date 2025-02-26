<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $result = $this->authService->register($request->validated());
            return response()->json([
                'message' => 'Register successful.',
                'data' => $result
            ], 201);
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => $exception->errors()
            ], 403);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());
            return response()->json([
                'message' => 'Login successful.',
                'data' => $result
            ], 200);
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => $exception->errors()
            ], 403);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->authService->forgotPassword($request->email);
        return response()->success(200, 'Password reset link sent to your email.', null);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword($request->validated());
        return response()->success(200, 'Password reset successful.', null);
    }

    public function logout(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = $request->user()->currentAccessToken();

        if (!$token) {
            return response()->json(['error' => 'Token not found'], 400);
        }

        $this->authService->logout($token);

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
