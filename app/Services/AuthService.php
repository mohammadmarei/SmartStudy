<?php

namespace App\Services;
use App\Models\User;
use DB;
use Hash;

class AuthService
{
    public function login($data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::where('email', $data->email)->first();
            if (!$user || !Hash::check($data->password, $user->password_hash)) {
                return [
                    "message" => "Email or password is incorrect",
                    'status' => 401
                ];
            } else {
                $token = $user->createToken('api-token')->plainTextToken;
                return [
                    "message" => "Login successful",
                    "status" => 200,
                    "token" => $token
                ];
            }
        });
    }
    public function register($data)
    {
        $user = User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'] ?? null,
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'role_id' => $data['role_id'] ?? 1,
            'agreed_to_terms' => $data['agreed_to_terms'] ?? false,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
