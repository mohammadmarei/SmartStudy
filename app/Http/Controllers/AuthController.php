<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {


        try {
            $login = $this->authService->login($request);

            if ($login['status'] === 401) {
                return response()->json([
                    'message' => $login['message'],
                ], 401);
            }



            if ($login['status'] !== 200) {
                return response()->json([
                    'message' => $login['message'],
                ], $login['status']);
            }

            $profileImage = $login['user']['profile']['avatar'] ?? null;

            return response()->json([
                'message' => $login['message'],
                'token'   => $login['token'],
                'user'    => [
                    'full_name'     => $login['user']['full_name'] ?? null,
                    'username'      => $login['user']['username'] ?? null,
                    'email'         => $login['user']['email'] ?? null,
                    'profile_image' => $profileImage,
                ],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Server error. Please try again later.'], 500);
        }
    }

    public function register(RegisterRequest $request)
    {


        try {
            $result = $this->authService->register($request->validated());

            return response()->json([
                'message' => 'User registered successfully.',
                'user'    => $result['user']->only(['id', 'full_name', 'username', 'email']),
                'token'   => $result['token'],
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'Email or username already exists.',
                ], 409);
            }

            Log::error('Register DB error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Server error. Please try again later.'], 500);

        } catch (\Throwable $e) {
            Log::error('Register error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Server error. Please try again later.'], 500);
        }
    }

    public function logout(Request $request)
    {

        try {
            $result = $this->authService->logout($request->user());

            return response()->json([
                'message' => $result['message'],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Logout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Server error. Please try again later.'], 500);
        }
    }
}
