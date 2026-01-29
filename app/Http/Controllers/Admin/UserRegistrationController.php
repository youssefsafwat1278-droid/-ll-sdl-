<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scout;

class UserRegistrationController extends Controller
{
    public function index()
    {
        // عرض كل الكشافة مع بيانات التسجيل (إن وجدت)
        $scouts = Scout::with(['user', 'patrol'])
            ->orderBy('role', 'asc') // القادة أولاً
            ->orderBy('patrol_id', 'asc')
            ->orderBy('first_name', 'asc')
            ->get();

        $registeredCount = $scouts->filter(fn($s) => $s->user !== null)->count();
        $totalCount = $scouts->count();

        return view('admin.user-registrations', compact('scouts', 'registeredCount', 'totalCount'));
    }
}
