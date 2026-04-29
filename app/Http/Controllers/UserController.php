<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(UpdateUserRequest $request)
    {
        $user = auth()->user();

        $user->update($request->only(['full_name', 'username', 'email']));

        $user->load('profile');

        return response()->json([
            'message' => 'User updated successfully.',
            'user'    => $user,
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 400);
        }

        $user->update([
            'password_hash' => Hash::make($request->password),
        ]);

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password changed successfully. Please login again.',
        ]);
    }

    public function destroy()
    {
        $user = auth()->user();

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully.',
        ]);
    }
}
