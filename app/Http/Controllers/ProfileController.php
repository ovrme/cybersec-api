<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('profile');
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone'   => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'bio'     => 'nullable|string',
        ]);

        $profile = UserProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->only('phone', 'country', 'bio')
        );

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile,
        ]);
    }
}
