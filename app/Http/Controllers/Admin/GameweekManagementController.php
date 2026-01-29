<?php
// app/Http/Controllers/Admin/GameweekManagementController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gameweek;
use App\Models\User;
use App\Models\Scout;
use App\Models\Patrol;
use App\Models\UserTeam;
use App\Models\ScoutGameweekPerformance;
use App\Models\OverallRanking;
use App\Models\PatrolRanking;
use App\Models\ChipUsage;
use App\Models\FreeHitSnapshot;
use App\Models\Transfer;
use App\Models\Notification;
use App\Models\UserGameweekPoint;
use App\Models\PatrolGameweekPoint;
use App\Models\TeamMemberPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameweekManagementController extends Controller
{
    public function index()
    {
        $gameweeks = Gameweek::orderBy('gameweek_number', 'desc')->get();
        $currentGameweek = Gameweek::where('is_current', true)->first();

        return view('admin.gameweeks.index', compact('gameweeks', 'currentGameweek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gameweek_number' => 'required|integer|min:1|unique:gameweeks,gameweek_number',
            'name' => 'nullable|string|max:100',
            'date' => 'required|date',
            'location' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'is_current' => 'nullable|boolean',
        ]);

        $isCurrent = $request->boolean('is_current');
        if ($isCurrent) {
            Gameweek::query()->update(['is_current' => false]);
        }

        $photoUrl = null;

        // Upload photo if provided
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'gameweek_' . $request->gameweek_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/gameweeks'), $filename);
            $photoUrl = '/images/gameweeks/' . $filename;
        }

        $gameweek = Gameweek::create([
            'gameweek_number' => $request->gameweek_number,
            'name' => $request->name,
            'date' => $request->date,
            'location' => $request->location,
            'photo_url' => $photoUrl,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'is_current' => $isCurrent,
            'is_finished' => false,
        ]);

        if ($isCurrent) {
            $this->ensureTeamsForCurrentGameweek($gameweek);
        }

        return redirect()->route('admin.gameweeks.index')->with('success', 'تم إضافة الجولة بنجاح.');
    }

    public function update(Request $request, $gameweekId)
    {
        $request->validate([
            'gameweek_number' => 'required|integer|min:1|unique:gameweeks,gameweek_number,' . $gameweekId,
            'name' => 'nullable|string|max:100',
            'date' => 'required|date',
            'location' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'is_current' => 'nullable|boolean',
        ]);

        $gameweek = Gameweek::findOrFail($gameweekId);
        $isCurrent = $request->boolean('is_current');

        if ($isCurrent) {
            Gameweek::query()->where('id', '!=', $gameweek->id)->update(['is_current' => false]);
        }

        $photoUrl = $gameweek->photo_url;

        // Upload new photo if provided
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($gameweek->photo_url && file_exists(public_path($gameweek->photo_url))) {
                unlink(public_path($gameweek->photo_url));
            }

            $file = $request->file('photo');
            $filename = 'gameweek_' . $request->gameweek_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/gameweeks'), $filename);
            $photoUrl = '/images/gameweeks/' . $filename;
        }

        $gameweek->update([
            'gameweek_number' => $request->gameweek_number,
            'name' => $request->name,
            'date' => $request->date,
            'location' => $request->location,
            'photo_url' => $photoUrl,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'is_current' => $isCurrent,
        ]);

        if ($isCurrent) {
            $this->ensureTeamsForCurrentGameweek($gameweek);
        }

        return redirect()->route('admin.gameweeks.index')->with('success', 'تم تحديث الجولة بنجاح.');
    }

    public function destroy($gameweekId)
    {
        $gameweek = Gameweek::findOrFail($gameweekId);
        $gameweek->delete();

        return back()->with('success', 'تم حذف الجولة بنجاح.');
    }


    public function refreshPoints(Request $request, $gameweekId)
    {
        set_time_limit(600); // 10 دقائق
        ini_set('memory_limit', '512M');

        try {
            DB::beginTransaction();

            $gameweek = Gameweek::findOrFail($gameweekId);

            if ($gameweek->is_finished) {
                return redirect()->back()->with('error', 'لا يمكن تحديث نقاط جولة منتهية.');
            }

            if (!$gameweek->is_current) {
                return redirect()->back()->with('error', 'يمكن تحديث النقاط للجولة الحالية فقط.');
            }

            $this->calculateUserPoints($gameweek);
            $this->calculatePatrolPoints($gameweek);
            $this->calculateRankings($gameweek);

            DB::commit();

            return redirect()->back()->with('success', 'تم تحديث نقاط الجولة بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refresh Points Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث النقاط: ' . $e->getMessage());
        }
    }

    public function finalize(Request $request, $gameweekId)
    {
        set_time_limit(1200); // 20 دقيقة
        ini_set('memory_limit', '512M'); // زيادة الذاكرة المتاحة
        try {
            DB::beginTransaction();

            $gameweek = Gameweek::findOrFail($gameweekId);

            if ($gameweek->is_finished) {
                return redirect()->back()->with('error', 'تم إنهاء هذه الجولة بالفعل.');
            }

            if (!$gameweek->is_current) {
                return redirect()->back()->with('error', 'لا يمكن إنهاء جولة غير مفعلة.');
            }

            $this->calculateUserPoints($gameweek);
            $this->calculatePatrolPoints($gameweek);
            $this->updateScoutPrices($gameweek);
            $this->calculateRankings($gameweek);
            $this->resetForNextGameweek($gameweek);
            $this->copyTeamsToNextGameweek($gameweek);

            $gameweek->update([
                'is_finished' => true,
                'is_current' => false,
            ]);

            $nextGameweek = Gameweek::where('gameweek_number', $gameweek->gameweek_number + 1)->first();
            if ($nextGameweek) {
                $nextGameweek->update(['is_current' => true]);
            }

            DB::commit();

            // إرسال الإشعارات بعد الـ commit (خارج الـ transaction)
            try {
                $this->sendNotifications($gameweek);
            } catch (\Exception $e) {
                Log::error('Send Notifications Error: ' . $e->getMessage());
            }

            return redirect()->route('admin.dashboard')->with('success', 'تم إنهاء الجولة بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Finalize Gameweek Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء الإنهاء: ' . $e->getMessage());
        }
    }

    private function calculateUserPoints($gameweek)
    {
        // Eager load everything needed for calculation
        $users = User::where('role', 'user')
            ->with([
                'teams' => function($q) use ($gameweek) {
                    $q->where('gameweek_id', $gameweek->id)->with('scout');
                },
                'transfers' => function($q) use ($gameweek) {
                    $q->where('gameweek_id', $gameweek->id);
                },
                'chipUsages' => function($q) use ($gameweek) {
                    $q->where('gameweek_id', $gameweek->id);
                }
            ])
            ->get();

        $originalFreeTransfers = 3; // الحد المجاني الأصلي للتبديلات

        // Pre-fetch scout performances for the gameweek to avoid querying in loop
        $performances = ScoutGameweekPerformance::where('gameweek_id', $gameweek->id)
            ->get()
            ->keyBy('scout_id');

        foreach ($users as $user) {
            // Use eager loaded teams relationship
            $team = $user->teams;

            if ($team->isEmpty()) {
                continue;
            }

            // حساب نقاط الفريق
            $teamPoints = 0;
            $captainMultiplier = 2;

            // التحقق من استخدام Triple Captain using filtered relation
            $tripleCapUsed = $user->chipUsages
                ->where('chip_type', 'triple_captain')
                ->isNotEmpty();

            $freeHitUsed = $user->chipUsages
                ->where('chip_type', 'free_hit')
                ->isNotEmpty();

            if ($tripleCapUsed) {
                $captainMultiplier = 3;
            }

            // حساب نقاط كل لاعب في الفريق
            foreach ($team as $teamMember) {
                // Use pre-fetched performances
                $performance = $performances->get($teamMember->scout_id);

                $basePoints = $performance ? $performance->total_points : 0;
                $points = $basePoints;

                // مضاعفة نقاط الكابتن
                if ($teamMember->is_captain) {
                    $points *= $captainMultiplier;
                }

                $teamPoints += $points;
            }

            // حساب عقوبة التبديلات using eager loaded count
            $transferCount = $user->transfers->count();

            $transferPenalty = 0;
            if (!$freeHitUsed && $transferCount > $originalFreeTransfers) {
                $transferPenalty = ($transferCount - $originalFreeTransfers) * 4;
            }

            // حساب النقاط الصافية (تسمح بالسالب)
            $netPoints = $teamPoints - $transferPenalty;

            // حساب إجمالي النقاط حتى هذه الجولة
            // This still queries, but it's a sum, maybe acceptable or optimize later if needed.
            // Ideally we keep running total in user model which we update anyway.
            $previousTotal = UserGameweekPoint::where('user_id', $user->id)
                ->where('gameweek_id', '<', $gameweek->id)
                ->sum('net_points');

            $totalPointsAfter = $previousTotal + $netPoints;

            // حفظ/تحديث نقاط الجولة في user_gameweek_points
            UserGameweekPoint::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'gameweek_id' => $gameweek->id,
                ],
                [
                    'team_points' => $teamPoints,
                    'transfer_penalty' => $transferPenalty,
                    'net_points' => $netPoints,
                    'total_points_after' => $totalPointsAfter,
                    'rank_in_gameweek' => 0, // سيتم تحديثه في calculateRankings
                ]
            );

            // تحديث جدول users بالنقاط المحسوبة
            $user->update([
                'gameweek_points' => $netPoints,
                'total_points' => $totalPointsAfter,
            ]);
        }
    }

    private function calculatePatrolPoints($gameweek)
    {
        $patrols = Patrol::all();

        // جلب نقاط المستخدمين للجولة الحالية
        $gameweekUserPoints = UserGameweekPoint::where('gameweek_id', $gameweek->id)
            ->with('user:id,patrol_id')
            ->get()
            ->groupBy(function($point) {
                return $point->user->patrol_id;
            });

        // جلب إجمالي النقاط لكل طليعة
        $totalUserPoints = UserGameweekPoint::with('user:id,patrol_id')
            ->get()
            ->groupBy(function($point) {
                return $point->user->patrol_id;
            });

        $patrolGameweekPointsToInsert = [];
        $patrolRankingsToInsert = [];

        foreach ($patrols as $patrol) {
            // حساب نقاط الطليعة للجولة الحالية
            $gameweekPoints = $gameweekUserPoints->get($patrol->patrol_id)?->sum('net_points') ?? 0;

            // حساب إجمالي نقاط الطليعة
            $totalPoints = $totalUserPoints->get($patrol->patrol_id)?->sum('net_points') ?? 0;

            // تحديث إجمالي نقاط الطليعة
            $patrol->update([
                'total_points' => $totalPoints,
            ]);

            $patrolGameweekPointsToInsert[] = [
                'patrol_id' => $patrol->patrol_id,
                'gameweek_id' => $gameweek->id,
                'gameweek_points' => $gameweekPoints,
                'total_points_after' => $totalPoints,
                'rank' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $patrolRankingsToInsert[] = [
                'patrol_id' => $patrol->patrol_id,
                'gameweek_id' => $gameweek->id,
                'rank' => 0,
                'total_points' => $totalPoints,
                'gameweek_points' => $gameweekPoints,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // حذف البيانات القديمة وإدخال الجديدة
        PatrolGameweekPoint::where('gameweek_id', $gameweek->id)->delete();
        PatrolRanking::where('gameweek_id', $gameweek->id)->delete();

        if (!empty($patrolGameweekPointsToInsert)) {
            PatrolGameweekPoint::insert($patrolGameweekPointsToInsert);
        }

        if (!empty($patrolRankingsToInsert)) {
            PatrolRanking::insert($patrolRankingsToInsert);
        }
    }

    private function updateScoutPrices($gameweek)
    {
        $scouts = Scout::all();
        $totalUsers = User::where('role', 'user')->count();

        // جلب كل الأداءات مرة واحدة
        $performances = ScoutGameweekPerformance::where('gameweek_id', $gameweek->id)
            ->get()
            ->keyBy('scout_id');

        foreach ($scouts as $scout) {
            $performance = $performances->get($scout->scout_id);
            $points = $performance ? $performance->total_points : 0;
            $ownershipPercentage = ($scout->ownership_count / max($totalUsers, 1)) * 100;

            $newPrice = $scout->current_price;
            $reason = null;

            $isLeader = $scout->isLeaderOrSenior();
            $totalOwners = $isLeader
                ? (int) $scout->ownership_count
                : (int) $scout->local_ownership_count + (int) $scout->external_ownership_count;

            if ($points > 25) {
                $newPrice = min($scout->current_price + 0.5, 12.0);
                $reason = 'exceptional_performance';
            } elseif ($isLeader) {
                if ($points > 10 && $totalOwners > 20) {
                    $newPrice = min($scout->current_price + 0.5, 12.0);
                    $reason = 'leader_high_points_high_owners';
                } elseif ($points < 10 && $totalOwners < 20) {
                    $newPrice = max($scout->current_price - 0.5, 5.0);
                    $reason = 'leader_low_points_low_owners';
                }
            } else {
                $highPoints = $points > 20;
                $lowPoints = $points < 20;
                $highOwners = $totalOwners > 4;
                $lowOwners = $totalOwners < 4;

                if ($highPoints && $highOwners) {
                    $newPrice = min($scout->current_price + 0.5, 12.0);
                    $reason = 'high_points_high_owners';
                } elseif ($lowPoints && $lowOwners) {
                    $newPrice = max($scout->current_price - 0.5, 5.0);
                    $reason = 'low_points_low_owners';
                }
            }

            if ($newPrice != $scout->current_price) {
                $scout->updatePrice($newPrice, $gameweek->id, $reason);
            }

            $scout->update([
                'gameweek_points' => $points,
                'total_points' => $scout->total_points + $points,
                'form' => $scout->calculateForm(),
                'previous_ownership_count' => $scout->ownership_count,
                'ownership_percentage' => round($ownershipPercentage, 2),
            ]);
        }
    }

    private function calculateRankings($gameweek)
    {
        // حساب ترتيب المستخدمين حسب الإجمالي
        $users = User::where('role', 'user')
            ->orderBy('total_points', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        $rankingsToInsert = [];
        $rank = 1;

        foreach ($users as $user) {
            $rankingsToInsert[] = [
                'user_id' => $user->id,
                'gameweek_id' => $gameweek->id,
                'overall_rank' => $rank,
                'total_points' => (int) $user->total_points,
                'gameweek_points' => (int) $user->gameweek_points,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $rank++;
        }

        // حذف الترتيبات القديمة وإدخال الجديدة
        OverallRanking::where('gameweek_id', $gameweek->id)->delete();
        if (!empty($rankingsToInsert)) {
            foreach (array_chunk($rankingsToInsert, 500) as $chunk) {
                OverallRanking::insert($chunk);
            }
        }

        // حساب ترتيب المستخدمين حسب نقاط الجولة
        $gameweekRanking = UserGameweekPoint::where('gameweek_id', $gameweek->id)
            ->orderBy('net_points', 'desc')
            ->orderBy('user_id', 'asc')
            ->get();

        $gameweekRank = 1;
        foreach ($gameweekRanking as $userGwPoint) {
            $userGwPoint->rank_in_gameweek = $gameweekRank;
            $gameweekRank++;
        }

        // حفظ التحديثات دفعة واحدة
        foreach ($gameweekRanking as $userGwPoint) {
            $userGwPoint->save();
        }

        // حساب ترتيب الطلائع
        $patrols = Patrol::orderBy('total_points', 'desc')
            ->orderBy('patrol_id', 'asc')
            ->get();

        $rank = 1;
        foreach ($patrols as $patrol) {
            $patrol->rank = $rank;
            $patrol->save();

            PatrolRanking::where('patrol_id', $patrol->patrol_id)
                ->where('gameweek_id', $gameweek->id)
                ->update(['rank' => $rank]);

            $rank++;
        }
    }

    private function resetForNextGameweek($gameweek)
    {
        User::where('role', 'user')->update(['free_transfers' => 3]);
    }

    private function copyTeamsToNextGameweek($gameweek)
    {
        $nextGameweek = Gameweek::where('gameweek_number', $gameweek->gameweek_number + 1)->first();

        if (!$nextGameweek) {
            return;
        }

        $freeHitUsers = ChipUsage::where('gameweek_id', $gameweek->id)
            ->where('chip_type', 'free_hit')
            ->pluck('user_id')
            ->toArray();

        // حذف الفرق الموجودة مسبقاً في الجولة القادمة
        UserTeam::where('gameweek_id', $nextGameweek->id)->delete();

        $users = User::where('role', 'user')->pluck('id');

        // تجميع كل البيانات المطلوبة
        $scoutPrices = Scout::pluck('current_price', 'scout_id')->toArray();
        $teamsToInsert = [];

        foreach ($users as $userId) {
            $sourceTeam = null;

            if (in_array($userId, $freeHitUsers)) {
                $snapshot = FreeHitSnapshot::where('user_id', $userId)
                    ->where('gameweek_id', $gameweek->id)
                    ->orderBy('position_in_squad')
                    ->get();

                if ($snapshot->isNotEmpty()) {
                    $sourceTeam = $snapshot;
                } else {
                    $previousGameweek = Gameweek::where('gameweek_number', $gameweek->gameweek_number - 1)->first();
                    if ($previousGameweek) {
                        $sourceTeam = UserTeam::where('user_id', $userId)
                            ->where('gameweek_id', $previousGameweek->id)
                            ->get();
                    }
                }
            } else {
                $sourceTeam = UserTeam::where('user_id', $userId)
                    ->where('gameweek_id', $gameweek->id)
                    ->get();
            }

            if (!$sourceTeam || $sourceTeam->isEmpty()) {
                continue;
            }

            foreach ($sourceTeam as $teamMember) {
                $currentPrice = $scoutPrices[$teamMember->scout_id] ?? $teamMember->current_price;
                $purchasePrice = $teamMember->purchase_price ?? $currentPrice;

                $teamsToInsert[] = [
                    'user_id' => $userId,
                    'gameweek_id' => $nextGameweek->id,
                    'scout_id' => $teamMember->scout_id,
                    'position_in_squad' => $teamMember->position_in_squad,
                    'is_captain' => $teamMember->is_captain,
                    'is_vice_captain' => $teamMember->is_vice_captain,
                    'purchase_price' => $purchasePrice,
                    'current_price' => $currentPrice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // إدخال كل الفرق مرة واحدة
        if (!empty($teamsToInsert)) {
            // تقسيم البيانات إلى chunks لتجنب مشاكل الذاكرة
            foreach (array_chunk($teamsToInsert, 500) as $chunk) {
                UserTeam::insert($chunk);
            }
        }
    }

    private function ensureTeamsForCurrentGameweek(Gameweek $gameweek)
    {
        $sourceGameweek = Gameweek::where('gameweek_number', '<', $gameweek->gameweek_number)
            ->orderBy('gameweek_number', 'desc')
            ->first();

        if (!$sourceGameweek) {
            return;
        }

        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            $hasTeam = UserTeam::where('user_id', $user->id)
                ->where('gameweek_id', $gameweek->id)
                ->exists();

            if ($hasTeam) {
                continue;
            }

            // التحقق إذا كان المستخدم استخدم Free Hit في الجولة المصدر
            $usedFreeHitInSource = ChipUsage::where('user_id', $user->id)
                ->where('gameweek_id', $sourceGameweek->id)
                ->where('chip_type', 'free_hit')
                ->exists();

            $sourceTeam = null;

            if ($usedFreeHitInSource) {
                $snapshot = FreeHitSnapshot::where('user_id', $user->id)
                    ->where('gameweek_id', $sourceGameweek->id)
                    ->orderBy('position_in_squad')
                    ->get();

                if ($snapshot->isNotEmpty()) {
                    $sourceTeam = $snapshot;
                } else {
                    // إذا استخدم Free Hit في آخر جولة، ناخد الفريق من الجولة اللي قبلها
                    $actualSourceGameweek = Gameweek::where('gameweek_number', $sourceGameweek->gameweek_number - 1)->first();
                    if ($actualSourceGameweek) {
                        $sourceGameweek = $actualSourceGameweek;
                    }
                }
            }

            if (!$sourceTeam) {
                $sourceTeam = UserTeam::where('user_id', $user->id)
                    ->where('gameweek_id', $sourceGameweek->id)
                    ->get();
            }

            if ($sourceTeam->isEmpty()) {
                continue;
            }

            foreach ($sourceTeam as $teamMember) {
                $scout = Scout::find($teamMember->scout_id);

                UserTeam::create([
                    'user_id' => $user->id,
                    'gameweek_id' => $gameweek->id,
                    'scout_id' => $teamMember->scout_id,
                    'position_in_squad' => $teamMember->position_in_squad,
                    'is_captain' => $teamMember->is_captain,
                    'is_vice_captain' => $teamMember->is_vice_captain,
                    'purchase_price' => $teamMember->purchase_price,
                    'current_price' => $scout ? $scout->current_price : $teamMember->current_price,
                ]);
            }
        }
    }

    private function sendNotifications($gameweek)
    {
        $users = User::where('role', 'user')
            ->where('notifications_enabled', true)
            ->pluck('id', 'id');

        if ($users->isEmpty()) {
            return;
        }

        // جلب كل الترتيبات مرة واحدة
        $rankings = OverallRanking::where('gameweek_id', $gameweek->id)
            ->whereIn('user_id', $users->keys())
            ->get()
            ->keyBy('user_id');

        $notificationsToInsert = [];

        foreach ($users as $userId) {
            $ranking = $rankings->get($userId);

            if ($ranking) {
                $notificationsToInsert[] = [
                    'user_id' => $userId,
                    'type' => 'ranking',
                    'title' => 'ترتيبك العام',
                    'message' => "ترتيبك العام: #{$ranking->overall_rank} - نقاطك الإجمالية {$ranking->total_points} نقطة",
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // جلب تغييرات الأسعار مرة واحدة
        $priceChanges = UserTeam::whereIn('user_id', $users->keys())
            ->where('gameweek_id', $gameweek->id)
            ->with(['scout' => function($q) {
                $q->where('price_change', '!=', 0);
            }])
            ->get()
            ->filter(function ($team) {
                return $team->scout && $team->scout->price_change != 0;
            });

        foreach ($priceChanges as $team) {
            $icon = $team->scout->price_change > 0 ? '▲' : '▼';
            $notificationsToInsert[] = [
                'user_id' => $team->user_id,
                'type' => 'price_alert',
                'title' => "{$icon} تنبيه سعر",
                'message' => "{$team->scout->full_name}: {$team->scout->current_price} ({$team->scout->price_change_icon})",
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // إدخال كل الإشعارات مرة واحدة
        if (!empty($notificationsToInsert)) {
            foreach (array_chunk($notificationsToInsert, 500) as $chunk) {
                Notification::insert($chunk);
            }
        }
    }
}
