<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)
                ->with('profile')
                ->first();

            if (!$user || !Hash::check($request->password, $user->password_hash)) {
                return response()->json([
                    'message' => 'Email or password is incorrect.',
                ], 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;
            $profileImage = $user->profile?->avatar ?? null;

            return response()->json([
                'message' => 'Login successful.',
                'token'   => $token,
                'user'    => [
                    'full_name'     => $user->full_name,
                    'username'      => $user->username,
                    'email'         => $user->email,
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
            $data = $request->validated();

            $result = DB::transaction(function () use ($data) {
                $user = User::create([
                    'full_name'       => $data['full_name'],
                    'username'        => $this->generateUsername($data['full_name']),
                    'email'           => $data['email'],
                    'password_hash'   => Hash::make($data['password']),
                    'role_id'         => $data['role_id'] ?? 1,
                    'agreed_to_terms' => $data['agreed_to_terms'] ?? false,
                ]);

                Profile::create(['user_id' => $user->id]);

                $token = $user->createToken('api-token')->plainTextToken;

                return ['user' => $user, 'token' => $token];
            });

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
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully.',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Logout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Server error. Please try again later.'], 500);
        }
    }


    private function generateUsername(string $fullName): string
    {
        $base = trim(str_replace(' ', '.', strtolower(trim($fullName))), '.');

        $username = $base;
        $counter  = 1;

        while (User::withTrashed()->where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }
}
