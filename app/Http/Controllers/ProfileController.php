<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('profile');

        return response()->json([
            'message' => 'Profile retrieved successfully.',
            'user'    => $user,
        ], 200);
    }

    public function upsert(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->only([
            'date_of_birth',
            'gender',
            'bio',
            'linkedin_url',
            'github_url'
        ]);

        if ($request->hasFile('avatar')) {
            $existingProfile = $user->profile;

            if ($existingProfile && $existingProfile->avatar) {
                Storage::disk('public')->delete($existingProfile->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // نعرف إذا كان البروفايل موجود قبل
        $exists = Profile::where('user_id', $user->id)->exists();

        $profile = Profile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );


        $statusCode = $exists ? 200 : 201;

        return response()->json([
            'message' => $exists ? 'Profile updated successfully.' : 'Profile created successfully.',
            'profile' => $profile,
        ], $statusCode);
    }
}
