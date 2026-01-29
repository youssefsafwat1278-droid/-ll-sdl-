<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scout;
use App\Models\Gameweek;
use App\Models\ScoutGameweekPerformance;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    /**
     * Display the general summary page
     */
    public function index(Request $request)
    {
        // Get current active gameweek
        $currentGameweek = Gameweek::where('is_current', true)->first();

        // Get all gameweeks for filter dropdown
        $gameweeks = Gameweek::orderBy('gameweek_number', 'desc')->get();

        // Determine which gameweek to show
        $selectedGameweekId = $request->get('gameweek_id');

        // If no gameweek selected and not explicitly set to empty, use current gameweek
        if ($selectedGameweekId === null && $currentGameweek) {
            $selectedGameweekId = $currentGameweek->id;
        }

        // Convert empty string to null
        if ($selectedGameweekId === '') {
            $selectedGameweekId = null;
        }

        // Fetch scouts with their patrol and performance data
        $scouts = Scout::with([
            'patrol',
            'performances' => function($query) use ($selectedGameweekId) {
                if ($selectedGameweekId) {
                    $query->where('gameweek_id', $selectedGameweekId);
                }
            }
        ])
        ->orderBy('patrol_id')
        ->orderBy('first_name')
        ->get();

        return view('admin.summary', compact(
            'scouts',
            'gameweeks',
            'currentGameweek',
            'selectedGameweekId'
        ));
    }

    /**
     * Display detailed performance for a specific scout
     */
    public function show($scoutId)
    {
        // Get the scout with patrol relationship
        $scout = Scout::with('patrol')->findOrFail($scoutId);

        // Get all performances for this scout with gameweek info
        $performances = ScoutGameweekPerformance::where('scout_id', $scoutId)
            ->with('gameweek')
            ->orderBy('gameweek_id', 'desc')
            ->get();

        // Get all gameweeks
        $gameweeks = Gameweek::orderBy('gameweek_number', 'desc')->get();

        // Calculate statistics
        $totalGames = $performances->count();
        $avgPoints = $totalGames > 0 ? round($performances->avg('total_points'), 1) : 0;
        $bestPerformance = $performances->sortByDesc('total_points')->first();
        $worstPerformance = $performances->where('total_points', '>', 0)->sortBy('total_points')->first();

        // Category breakdown (sum of all gameweeks)
        $categoryTotals = [
            'attendance_points' => $performances->sum('attendance_points'),
            'interaction_points' => $performances->sum('interaction_points'),
            'uniform_points' => $performances->sum('uniform_points'),
            'activity_points' => $performances->sum('activity_points'),
            'service_points' => $performances->sum('service_points'),
            'committee_points' => $performances->sum('committee_points'),
            'mass_points' => $performances->sum('mass_points'),
            'confession_points' => $performances->sum('confession_points'),
            'group_mass_points' => $performances->sum('group_mass_points'),
            'tribe_mass_points' => $performances->sum('tribe_mass_points'),
            'aswad_points' => $performances->sum('aswad_points'),
            'first_group_points' => $performances->sum('first_group_points'),
            'largest_patrol_points' => $performances->sum('largest_patrol_points'),
            'penalty_points' => $performances->sum('penalty_points'),
        ];

        return view('admin.scout-details', compact(
            'scout',
            'performances',
            'gameweeks',
            'totalGames',
            'avgPoints',
            'bestPerformance',
            'worstPerformance',
            'categoryTotals'
        ));
    }

    /**
     * Display attendance and participation statistics for all scouts
     */
    public function attendanceStats(Request $request)
    {
        // Get all scouts with their performances
        $scouts = Scout::with(['patrol', 'performances'])->get();

        // Calculate statistics for each scout
        $scoutsStats = $scouts->map(function($scout) {
            $performances = $scout->performances;

            return [
                'scout' => $scout,
                'total_gameweeks' => $performances->count(),

                // Count occurrences (not sum of points)
                'attendance_count' => $performances->where('attendance_points', '>', 0)->count(),
                'attendance_absent' => $performances->where('attendance_points', '<', 0)->count(),
                'interaction_count' => $performances->where('interaction_points', '>', 0)->count(),
                'uniform_count' => $performances->where('uniform_points', '>', 0)->count(),
                'activity_count' => $performances->where('activity_points', '>', 0)->count(),
                'service_count' => $performances->where('service_points', '>', 0)->count(),
                'committee_count' => $performances->where('committee_points', '>', 0)->count(),
                'mass_count' => $performances->where('mass_points', '>', 0)->count(),
                'confession_count' => $performances->where('confession_points', '>', 0)->count(),
                'group_mass_count' => $performances->where('group_mass_points', '>', 0)->count(),
                'tribe_mass_count' => $performances->where('tribe_mass_points', '>', 0)->count(),
                'aswad_count' => $performances->where('aswad_points', '>', 0)->count(),
                'first_group_count' => $performances->where('first_group_points', '>', 0)->count(),
                'largest_patrol_count' => $performances->where('largest_patrol_points', '>', 0)->count(),
                'penalty_count' => $performances->where('penalty_points', '<', 0)->count(),

                // Total points
                'total_points' => $scout->total_points ?? 0,
            ];
        });

        // Sort by total gameweeks (most active first)
        $scoutsStats = $scoutsStats->sortByDesc('total_gameweeks');

        return view('admin.attendance-stats', compact('scoutsStats'));
    }
}
