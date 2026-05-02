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

        $newData = $request->only(['full_name', 'username', 'email']);

        if (!$user->isDirty($newData) && $user->fill($newData)->isDirty() === false) {
            return response()->json([
                'message' => 'No changes detected.',
                'user'    => $user->load('profile'),
            ], 200);
        }

        $user->update($newData);
        $user->load('profile');

        return response()->json([
            'message' => 'User updated successfully.',
            'user'    => $user,
        ], 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 401);
        }

        if (Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'message' => 'New password must be different from the current password.',
            ], 422);
        }

        $user->update([
            'password_hash' => Hash::make($request->password),
        ]);

        $currentTokenId = $user->currentAccessToken()->id;
        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        return response()->json([
            'message' => 'Password changed successfully.',
        ], 200);
    }

    public function destroy()
    {
        $user = auth()->user();


        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully.',
        ], 200);
    }
}
