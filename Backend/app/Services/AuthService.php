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
            if (!$user || !Hash::check($data->password, $user->password)) {
                return [
                    "message" => "Email or password is incorrect",
                    'status' => 401
                ];
            } else {
                $attendance = DB::table('attendance_logs')->where('user_id', $user->id)->where("active", true)->first();
                if ($attendance) {
                    return [
                        "message" => "User already logged in ",
                        "status" => 409
                    ];
                } else {
                    DB::table('attendance_logs')->insert(['user_id' => $user->id, 'work_date' => date('Y-m-d'), 'check_in_at' => now(), 'active' => true]);
                    $token = $user->createToken('api-token')->plainTextToken;
                    return [
                        "message" => "Login successful",
                        "status" => 200,
                        "token" => $token
                    ];
                }
            }
        });
    }

}
