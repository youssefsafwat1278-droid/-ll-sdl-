<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Scout;
use App\Models\Gameweek;
use App\Models\Transfer;
use App\Models\ScoutGameweekPerformance;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalScouts = Scout::count();
        $currentGameweek = Gameweek::where('is_current', true)->first();
        
        $usersWithTeams = 0;
        $performancesEntered = 0;

        if ($currentGameweek) {
            $usersWithTeams = User::where('role', 'user')
                ->whereHas('teams', function($q) use ($currentGameweek) {
                    $q->where('gameweek_id', $currentGameweek->id);
                })
                ->count();

            $performancesEntered = ScoutGameweekPerformance::where('gameweek_id', $currentGameweek->id)->count();
        }

        $totalTransfers = Transfer::count();
        $recentTransfers = Transfer::with(['user.scout', 'scoutOut', 'scoutIn', 'gameweek'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalScouts',
            'currentGameweek',
            'usersWithTeams',
            'performancesEntered',
            'totalTransfers',
            'recentTransfers'
        ));
    }
}
