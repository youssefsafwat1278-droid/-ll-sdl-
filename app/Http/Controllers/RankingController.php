<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OverallRanking;
use App\Models\Gameweek;
use App\Models\UserGameweekPoint;

class RankingController extends Controller
{
    public function overall()
    {
        // جلب المستخدمين مرتبين حسب إجمالي النقاط من جدول users
        $users = User::where('role', 'user')
            ->with(['scout', 'patrol'])
            ->orderBy('total_points', 'desc')
            ->paginate(50);

        // إنشاء map للنقاط الإجمالية (من جدول users مباشرة)
        $totalPointsMap = $users->pluck('total_points', 'id')->toArray();

        $currentGameweek = Gameweek::where('is_current', true)->first();
        $gameweekPointsMap = [];
        $gameweekRanks = [];

        if ($currentGameweek) {
            // حساب نقاط الجولة الحالية لكل مستخدم
            $allUsers = User::where('role', 'user')->get();
            $gameweekScores = [];

            foreach ($allUsers as $user) {
                $gameweekScores[$user->id] = (int) $user->gameweek_points;
            }

            // ترتيب المستخدمين حسب نقاط الجولة
            arsort($gameweekScores);

            $rank = 1;
            foreach ($gameweekScores as $userId => $points) {
                $gameweekPointsMap[$userId] = $points;
                $gameweekRanks[$userId] = $rank;
                $rank++;
            }
        }

        return view('rankings', compact('users', 'gameweekRanks', 'totalPointsMap', 'gameweekPointsMap'));
    }

    public function myRanking()
    {
        $user = auth()->user();

        $rankings = OverallRanking::where('user_id', $user->id)
            ->with('gameweek')
            ->orderBy('gameweek_id', 'desc')
            ->get();

        $currentGameweek = Gameweek::where('is_current', true)->first();
        $currentGameweekRank = null;

        if ($currentGameweek) {
            // حساب ترتيب المستخدم في الجولة الحالية من جدول users
            $allUsers = User::where('role', 'user')
                ->orderBy('gameweek_points', 'desc')
                ->pluck('id')
                ->toArray();

            $currentGameweekRank = array_search($user->id, $allUsers);
            $currentGameweekRank = $currentGameweekRank === false ? null : $currentGameweekRank + 1;
        }

        return view('my-ranking', compact('user', 'rankings', 'currentGameweekRank'));
    }

    public function topGameweek()
    {
        $currentGameweek = Gameweek::where('is_current', true)->first();

        if (!$currentGameweek) {
            return view('top-gameweek', ['message' => 'لا يوجد أسبوع نشط حاليا']);
        }

        // جلب أفضل 10 مستخدمين حسب نقاط الجولة الحالية
        $topUsers = User::where('role', 'user')
            ->with(['scout', 'patrol'])
            ->orderBy('gameweek_points', 'desc')
            ->limit(10)
            ->get();

        return view('top-gameweek', compact('topUsers', 'currentGameweek'));
    }
}