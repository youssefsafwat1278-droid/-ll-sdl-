<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\UserGameweekPoint;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('patrol', 'scout.patrol');
        // استخدام النقاط الإجمالية من جدول users مباشرة
        $displayTotalPoints = (int) $user->total_points;

        return view('settings', compact('user', 'displayTotalPoints'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'team_name' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:190|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'theme' => 'nullable|in:light,dark',
            'language' => 'nullable|in:ar,en',
            'notifications_enabled' => 'nullable|boolean',
            'profile_public' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [];

        if ($request->filled('team_name')) {
            $data['team_name'] = $request->team_name;
        }

        if ($request->filled('email')) {
            $data['email'] = $request->email;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->filled('theme')) {
            $data['theme'] = $request->theme;
        }

        if ($request->filled('language')) {
            $data['language'] = $request->language;
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            $existingPhoto = $user->getRawOriginal('photo_url');
            if ($existingPhoto && file_exists(public_path($existingPhoto))) {
                unlink(public_path($existingPhoto));
            }

            $file = $request->file('photo');
            $filename = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Move file to public/images/users
            $file->move(public_path('images/users'), $filename);

            $photoUrl = '/images/users/' . $filename;
            $data['photo_url'] = $photoUrl;

            // Update scout photo_url as well (so it shows everywhere)
            if ($user->scout_id) {
                $scout = \App\Models\Scout::where('scout_id', $user->scout_id)->first();
                if ($scout) {
                    $scout->photo_url = $photoUrl;
                    $scout->save();
                }
            }
        }

        $data['notifications_enabled'] = $request->boolean('notifications_enabled');
        $data['profile_public'] = $request->boolean('profile_public');

        $user->update($data);

        return back()->with('success', 'تم تحديث الإعدادات بنجاح.');
    }
}
