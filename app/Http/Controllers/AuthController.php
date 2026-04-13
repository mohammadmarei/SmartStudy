<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginRequest;
use App\Http\Requests\registerRequest;
use App\Models\Auth;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(loginRequest $request)
    {
        try {
            $login = $this->authService->login($request);
            if ($login['status'] !== 200) {
                return response()->json(
                    ['message' => $login['message']],
                    $login['status']
                );
            } else {
                return response()->json([
                    'message' => $login['message'],
                    'token' => $login['token'],
                ], 200);

            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function register(registerRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $result['user'],
            'token' => $result['token']
        ]);
    }

}
