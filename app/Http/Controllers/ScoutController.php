<?php

namespace App\Http\Controllers;

use App\Models\Scout;
use App\Models\ScoutGameweekPerformance;
use App\Models\PriceHistory;
use Illuminate\Http\Request;

class ScoutController extends Controller
{
    public function index(Request $request)
    {
        $query = Scout::with('patrol');

        if ($request->filled('patrol')) {
            $query->where('patrol_id', $request->patrol);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('min_price')) {
            $query->where('current_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('current_price', '<=', $request->max_price);
        }

        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->where('is_available', true);
            } else {
                $query->where('is_available', false);
            }
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'points_desc':
                    $query->orderBy('total_points', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('current_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('current_price', 'desc');
                    break;
                case 'form_desc':
                    $query->orderBy('form', 'desc');
                    break;
                default:
                    $query->orderBy('total_points', 'desc');
            }
        } else {
            $query->orderBy('total_points', 'desc');
        }

        $scouts = $query->paginate(20);
        $patrols = \App\Models\Patrol::all();

        return view('scouts.index', compact('scouts', 'patrols'));
    }

    public function show($scoutId)
    {
        $scout = Scout::with('patrol')->findOrFail($scoutId);

        $performances = ScoutGameweekPerformance::where('scout_id', $scoutId)
            ->with('gameweek')
            ->orderBy('gameweek_id', 'desc')
            ->limit(10)
            ->get();

        $priceHistory = PriceHistory::where('scout_id', $scoutId)
            ->with('gameweek')
            ->orderBy('gameweek_id', 'asc')
            ->get();

        return view('scouts.show', compact('scout', 'performances', 'priceHistory'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $scouts = Scout::with('patrol')
            ->where(function($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhere('scout_id', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json($scouts);
    }
}