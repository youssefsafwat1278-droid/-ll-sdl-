<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Scout;
use App\Models\Transfer;
use App\Models\Gameweek;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        $currentGameweek = Gameweek::where('is_current', true)->first();

        $mostOwnedScouts = Scout::with('patrol')
            ->orderBy('ownership_count', 'desc')
            ->limit(10)
            ->get();

        $leastOwnedScouts = Scout::with('patrol')
            ->where('is_available', true)
            ->orderBy('ownership_count', 'asc')
            ->limit(10)
            ->get();

        $priceRisers = Scout::with('patrol')
            ->where('price_change', '>', 0)
            ->orderBy('price_change', 'desc')
            ->limit(5)
            ->get();

        $priceFallers = Scout::with('patrol')
            ->where('price_change', '<', 0)
            ->orderBy('price_change', 'asc')
            ->limit(5)
            ->get();

        $transferStats = [];
        if ($currentGameweek) {
            $transferStats['total'] = Transfer::where('gameweek_id', $currentGameweek->id)->count();
            $transferStats['average'] = $transferStats['total'] / max(User::where('role', 'user')->count(), 1);

            $transferStats['most_in'] = Transfer::where('gameweek_id', $currentGameweek->id)
                ->select('scout_in_id', DB::raw('COUNT(*) as count'))
                ->groupBy('scout_in_id')
                ->orderBy('count', 'desc')
                ->with('scoutIn')
                ->limit(5)
                ->get();

            $transferStats['most_out'] = Transfer::where('gameweek_id', $currentGameweek->id)
                ->select('scout_out_id', DB::raw('COUNT(*) as count'))
                ->groupBy('scout_out_id')
                ->orderBy('count', 'desc')
                ->with('scoutOut')
                ->limit(5)
                ->get();
        }

        return view('admin.statistics', compact(
            'mostOwnedScouts',
            'leastOwnedScouts',
            'priceRisers',
            'priceFallers',
            'transferStats',
            'currentGameweek'
        ));
    }
}
