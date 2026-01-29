<?php
// app/Http/Controllers/Admin/PointsController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scout;
use App\Models\Gameweek;
use App\Models\ScoutGameweekPerformance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class PointsController extends Controller
{
    public function index(Request $request)
    {
        $currentGameweek = Gameweek::where('is_current', true)->first();
        $gameweeks = Gameweek::orderBy('gameweek_number', 'desc')->get();

        $gameweekId = $request->get('gameweek_id', $currentGameweek?->id);

        if ($gameweeks->isEmpty()) {
            return view('admin.points.index', [
                'message' => 'لا يوجد أسابيع (Gameweeks) في قاعدة البيانات',
                'gameweeks' => $gameweeks,
                'gameweekId' => null,
                'scouts' => collect(),
                'enteredCount' => 0,
                'totalCount' => 0,
                'progress' => 0,
            ]);
        }

        if (!$gameweekId) {
            return view('admin.points.index', [
                'message' => 'لا يوجد أسبوع محدد',
                'gameweeks' => $gameweeks,
                'gameweekId' => $gameweeks->first()->id,
                'scouts' => collect(),
                'enteredCount' => 0,
                'totalCount' => 0,
                'progress' => 0,
            ]);
        }

        $scouts = Scout::with([
                'patrol',
                'performances' => function ($q) use ($gameweekId) {
                    $q->where('gameweek_id', $gameweekId);
                }
            ])
            ->withCount(['performances as has_points' => function ($q) use ($gameweekId) {
                $q->where('gameweek_id', $gameweekId);
            }])
            ->orderBy('scout_id')
            ->get();

        // إضافة النقاط لكل كشاف حسب الجولة المختارة
        $scouts->each(function ($scout) {
            $performance = $scout->performances->first();
            $scout->current_gameweek_points = $performance ? $performance->total_points : 0;
        });

        $enteredCount = $scouts->filter(function ($scout) {
            return (int) $scout->has_points > 0;
        })->count();
        $totalCount = $scouts->count();
        $progress = $totalCount > 0 ? round(($enteredCount / $totalCount) * 100, 1) : 0;

        return view('admin.points.index', compact(
            'scouts',
            'gameweeks',
            'gameweekId',
            'enteredCount',
            'totalCount',
            'progress'
        ));
    }

    public function show($scoutId, Request $request)
    {
        $currentGameweek = Gameweek::where('is_current', true)->first();
        $gameweekId = $request->get('gameweek_id', $currentGameweek?->id);

        $scout = Scout::with('patrol')->findOrFail($scoutId);
        $gameweek = Gameweek::findOrFail($gameweekId);

        $performance = ScoutGameweekPerformance::where('scout_id', $scoutId)
            ->where('gameweek_id', $gameweekId)
            ->first();

        return view('admin.points.form', compact('scout', 'gameweek', 'performance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'scout_id' => 'required|exists:scouts,scout_id',
            'gameweek_id' => 'required|exists:gameweeks,id',
            'attendance_points' => 'required|integer|in:-2,1,2',
            'interaction_points' => 'required|integer|min:0',
            'uniform_points' => 'required|integer|min:0',
            'activity_points' => 'required|integer|min:0',
            'service_points' => 'required|integer|min:0',
            'committee_points' => 'required|integer|min:0',
            'mass_points' => 'required|integer|min:0',
            'confession_points' => 'required|integer|min:0',
            'group_mass_points' => 'required|integer|min:0',
            'tribe_mass_points' => 'required|integer|min:0',
            'aswad_points' => 'required|integer|min:0',
            'first_group_points' => 'required|integer|min:0',
            'largest_patrol_points' => 'required|integer|min:0',
            'penalty_points' => 'required|integer|max:0',
            'notes' => 'nullable|string',
        ]);

        $totalPoints = $request->attendance_points +
            $request->interaction_points +
            $request->uniform_points +
            $request->activity_points +
            $request->service_points +
            $request->committee_points +
            $request->mass_points +
            $request->confession_points +
            $request->group_mass_points +
            $request->tribe_mass_points +
            $request->aswad_points +
            $request->first_group_points +
            $request->largest_patrol_points +
            $request->penalty_points;

        ScoutGameweekPerformance::updateOrCreate(
            [
                'scout_id' => $request->scout_id,
                'gameweek_id' => $request->gameweek_id,
            ],
            [
                'attendance_points' => $request->attendance_points,
                'interaction_points' => $request->interaction_points,
                'uniform_points' => $request->uniform_points,
                'activity_points' => $request->activity_points,
                'service_points' => $request->service_points,
                'committee_points' => $request->committee_points,
                'mass_points' => $request->mass_points,
                'confession_points' => $request->confession_points,
                'group_mass_points' => $request->group_mass_points,
                'tribe_mass_points' => $request->tribe_mass_points,
                'aswad_points' => $request->aswad_points,
                'first_group_points' => $request->first_group_points,
                'largest_patrol_points' => $request->largest_patrol_points,
                'penalty_points' => $request->penalty_points,
                'total_points' => $totalPoints,
                'notes' => $request->notes,
            ]
        );

        $gameweek = Gameweek::find($request->gameweek_id);
        if ($gameweek && $gameweek->is_current) {
            Scout::where('scout_id', $request->scout_id)
                ->update(['gameweek_points' => $totalPoints]);
        }

        return redirect()
            ->route('admin.points.index', ['gameweek_id' => $request->gameweek_id])
            ->with('success', 'تم حفظ نقاط الجولة بنجاح.');
    }

    public function destroy($scoutId, Request $request)
    {
        $gameweekId = $request->get('gameweek_id');

        ScoutGameweekPerformance::where('scout_id', $scoutId)
            ->where('gameweek_id', $gameweekId)
            ->delete();

        $gameweek = Gameweek::find($gameweekId);
        if ($gameweek && $gameweek->is_current) {
            Scout::where('scout_id', $scoutId)->update(['gameweek_points' => 0]);
        }

        return back()->with('success', 'تم حذف نقاط الجولة بنجاح.');
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
            'gameweek_id' => 'required|exists:gameweeks,id',
        ]);

        DB::beginTransaction();
        try {
            $imported = 0;
            $errors = [];

            Excel::import(new \App\Imports\PointsImport($request->gameweek_id, $imported, $errors), $request->file('excel_file'));

            DB::commit();

            if (count($errors) > 0) {
                return back()->with('warning', "تم استيراد {$imported} كشاف بنجاح، مع وجود " . count($errors) . " خطأ.");
            }

            return back()->with('success', "تم استيراد {$imported} كشاف بنجاح.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
