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
        ]);
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

        $profile = Profile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json([
            'message' => 'Profile updated successfully.',
            'profile' => $profile,
        ]);
    }
}
