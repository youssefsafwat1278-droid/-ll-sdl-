<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scout;
use App\Models\Patrol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScoutImageController extends Controller
{
    /**
     * Display scout image management page
     */
    public function index()
    {
        $scouts = Scout::with('patrol')
            ->orderBy('patrol_id')
            ->orderBy('first_name')
            ->get();

        $patrols = Patrol::all();

        return view('admin.scout-images', compact('scouts', 'patrols'));
    }

    /**
     * Upload scout photo
     */
    public function uploadScoutPhoto(Request $request, $scout)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048', // Max 2MB
        ]);

        $scoutModel = Scout::findOrFail($scout);

        // Delete old photo if exists
        $existingPhoto = $scoutModel->getRawOriginal('photo_url');
        if ($existingPhoto && file_exists(public_path($existingPhoto))) {
            unlink(public_path($existingPhoto));
        }

        // Upload new photo
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'scout_' . $scout . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Move file to public/images/scouts
            $file->move(public_path('images/scouts'), $filename);

            $photoUrl = '/images/scouts/' . $filename;

            // Update scout photo_url
            $scoutModel->photo_url = $photoUrl;
            $scoutModel->save();

            // Update user photo_url as well (if user exists for this scout)
            $user = \App\Models\User::where('scout_id', $scout)->first();
            if ($user) {
                $user->photo_url = $photoUrl;
                $user->save();
            }
        }

        return back()->with('success', 'تم رفع صورة الكشاف بنجاح');
    }

    /**
     * Delete scout photo
     */
    public function deleteScoutPhoto($scout)
    {
        $scoutModel = Scout::findOrFail($scout);

        $existingPhoto = $scoutModel->getRawOriginal('photo_url');
        if ($existingPhoto && file_exists(public_path($existingPhoto))) {
            unlink(public_path($existingPhoto));
        }

        $scoutModel->photo_url = null;
        $scoutModel->save();

        // Delete user photo_url as well (if user exists for this scout)
        $user = \App\Models\User::where('scout_id', $scout)->first();
        if ($user) {
            $user->photo_url = null;
            $user->save();
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'تم حذف صورة الكشاف بنجاح']);
        }

        return back()->with('success', 'تم حذف صورة الكشاف بنجاح');
    }

    /**
     * Upload patrol logo
     */
    public function uploadPatrolLogo(Request $request, $patrol)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:1024', // Max 1MB
        ]);

        $patrolModel = Patrol::findOrFail($patrol);

        // Delete old logo if exists
        if ($patrolModel->patrol_logo_url && file_exists(public_path($patrolModel->patrol_logo_url))) {
            unlink(public_path($patrolModel->patrol_logo_url));
        }

        // Upload new logo
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'patrol_' . $patrol . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Move file to public/images/patrols
            $file->move(public_path('images/patrols'), $filename);

            // Update patrol logo_url
            $patrolModel->patrol_logo_url = '/images/patrols/' . $filename;
            $patrolModel->save();
        }

        return back()->with('success', 'تم رفع شعار الطليعة بنجاح');
    }

    /**
     * Delete patrol logo
     */
    public function deletePatrolLogo($patrol)
    {
        $patrolModel = Patrol::findOrFail($patrol);

        if ($patrolModel->patrol_logo_url && file_exists(public_path($patrolModel->patrol_logo_url))) {
            unlink(public_path($patrolModel->patrol_logo_url));
        }

        $patrolModel->patrol_logo_url = null;
        $patrolModel->save();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'تم حذف شعار الطليعة بنجاح']);
        }

        return back()->with('success', 'تم حذف شعار الطليعة بنجاح');
    }
}
