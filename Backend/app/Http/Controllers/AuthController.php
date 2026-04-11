<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginRequest;
use App\Models\Auth;
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

}
