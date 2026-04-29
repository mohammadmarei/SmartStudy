<?php

namespace App\Services;

use App\Models\User;
use DB;
use Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Profile;
class AuthService
{
    public function login($data)
    {
        $user = User::where('email', $data->email)->first();

        if (!$user || !Hash::check($data->password, $user->password_hash)) {
            return [
                'message' => 'Email or password is incorrect',
                'status'  => 401,
            ];
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'message' => 'Login successful',
            'status'  => 200,
            'token'   => $token,
            'user'    => $user->only(['id', 'full_name', 'username', 'email']),
        ];
    }
    public function register($data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'full_name'       => $data['full_name'],
                'username'        => $this->generateUsername($data['full_name']),
                'email'           => $data['email'],
                'password_hash'   => Hash::make($data['password']),
                'role_id'         => $data['role_id'] ?? 1,
                'agreed_to_terms' => $data['agreed_to_terms'] ?? false,
            ]);
            Profile::create([
                'user_id' => $user->id,
            ]);
            $token = $user->createToken('api-token')->plainTextToken;

            return [
                'user'  => $user,
                'token' => $token,
            ];
        });
    }

    private function generateUsername(string $fullName): string
    {
        $base = str_replace(' ', '.', strtolower(trim($fullName)));
        $base = trim($base, '.');

        $username = $base;
        $counter = 1;

        while (User::withTrashed()->where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }


    public function logout($user)
    {
        $user->currentAccessToken()->delete();

        return ['message' => 'Logged out successfully'];
    }
}
