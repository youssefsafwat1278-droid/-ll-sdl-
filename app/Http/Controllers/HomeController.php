<?php

namespace App\Http\Controllers;

use App\Models\Scout;
use App\Models\Gameweek;
use App\Models\News;
use App\Models\OverallRanking;
use App\Models\UserGameweekPoint;
use App\Models\Notification;
use App\Models\Patrol;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $currentGameweek = Gameweek::where('is_current', true)->first();
        
        $topScouts = Scout::with('patrol')
            ->orderBy('total_points', 'desc')
            ->limit(5)
            ->get();

        $topPatrols = Patrol::orderBy('total_points', 'desc')
            ->limit(5)
            ->get();

        $featuredNews = News::featured()->latest()->limit(3)->get();

        $userRanking = null;
        if ($currentGameweek) {
            $userRanking = OverallRanking::where('user_id', $user->id)
                ->orderBy('gameweek_id', 'desc')
                ->first();
        }

        // استخدام النقاط الإجمالية من جدول users مباشرة
        $displayTotalPoints = (int) $user->total_points;

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $hasTeam = $currentGameweek ? $user->hasTeamForGameweek($currentGameweek->id) : false;

        return view('home', compact(
            'user',
            'currentGameweek',
            'topScouts',
            'topPatrols',
            'featuredNews',
            'userRanking',
            'displayTotalPoints',
            'notifications',
            'hasTeam'
        ));
    }

    public function notifications()
    {
        $user = auth()->user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return back()->with('success', 'تم تحديد الإشعار كمقروء');
    }
}
