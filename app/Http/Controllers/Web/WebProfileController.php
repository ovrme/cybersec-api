<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class WebProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('profile');

        return view('profile.show', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone'   => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'bio'     => 'nullable|string|max:1000',
        ]);

        UserProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->only('phone', 'country', 'bio'),
        );

        return back()->with('status', 'Profile updated.');
    }
}
