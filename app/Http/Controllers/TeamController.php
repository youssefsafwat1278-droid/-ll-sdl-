<?php
// app/Http/Controllers/TeamController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Scout;
use App\Models\Gameweek;
use App\Models\UserTeam;
use App\Models\UserGameweekPoint;
use App\Models\ScoutGameweekPerformance;
use App\Models\ChipUsage;
use App\Models\FreeHitSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $currentGameweek = Gameweek::where('is_current', true)->first();

        if (!$currentGameweek) {
            $team = UserTeam::where('user_id', $user->id)
                ->with('scout.patrol')
                ->orderBy('gameweek_id', 'desc')
                ->orderBy('position_in_squad')
                ->get();

            if ($team->isEmpty()) {
                return view('my-team', [
                    'hasTeam' => false,
                    'message' => 'لا يوجد فريق بعد.',
                    'user' => $user,
                    'displayTotalPoints' => 0,
                    'teamGameweekPoints' => 0,
                    'currentGameweek' => null,
                    'team' => collect(),
                    'captain' => null,
                    'viceCaptain' => null,
                    'tripleCapUsed' => false,
                    'hasUsedTripleCapBefore' => false,
                    'freeHitActive' => false,
                    'hasUsedFreeHitBefore' => false,
                ]);
            }

            $displayTotalPoints = (int) $user->total_points;

            $latestGameweekPoint = UserGameweekPoint::where('user_id', $user->id)
                ->orderBy('gameweek_id', 'desc')
                ->first();

            $teamGameweekPoints = $latestGameweekPoint ? $latestGameweekPoint->team_points : 0;

            $captain = $team->where('is_captain', true)->first();
            $viceCaptain = $team->where('is_vice_captain', true)->first();

            return view('my-team', [
                'team' => $team,
                'user' => $user,
                'displayTotalPoints' => $displayTotalPoints,
                'teamGameweekPoints' => $teamGameweekPoints,
                'currentGameweek' => $currentGameweek,
                'captain' => $captain,
                'viceCaptain' => $viceCaptain,
                'tripleCapUsed' => false,
                'hasUsedTripleCapBefore' => false,
                'freeHitActive' => false,
                'hasUsedFreeHitBefore' => false,
            ]);
        }

        $hasTeam = $user->hasTeamForGameweek($currentGameweek->id);
        if (!$hasTeam) {
            $this->ensureTeamForCurrentGameweek($user, $currentGameweek);
            $hasTeam = $user->hasTeamForGameweek($currentGameweek->id);
        }

        if (!$hasTeam) {
            return redirect('/team/builder')->with('info', 'ابدأ ببناء فريقك أولاً.');
        }

        $team = UserTeam::where('user_id', $user->id)
            ->where('gameweek_id', $currentGameweek->id)
            ->with('scout.patrol')
            ->orderBy('position_in_squad')
            ->get();

        $captain = $team->where('is_captain', true)->first();
        $viceCaptain = $team->where('is_vice_captain', true)->first();

        $displayTotalPoints = (int) $user->total_points;

        // قراءة نقاط الجولة من user_gameweek_points
        $gameweekPoint = UserGameweekPoint::where('user_id', $user->id)
            ->where('gameweek_id', $currentGameweek->id)
            ->first();

        $teamGameweekPoints = $gameweekPoint ? $gameweekPoint->team_points : 0;

        // إضافة نقاط الكشافين إلى كل عضو في الفريق للعرض
        foreach ($team as $member) {
            $performance = ScoutGameweekPerformance::where('scout_id', $member->scout_id)
                ->where('gameweek_id', $currentGameweek->id)
                ->first();

            $member->scout->gameweek_points = $performance ? $performance->total_points : 0;
        }

        // Check Chip Usage
        $tripleCapUsed = ChipUsage::where('user_id', $user->id)
            ->where('chip_type', 'triple_captain')
            ->where('gameweek_id', $currentGameweek->id)
            ->exists();

        $hasUsedTripleCapBefore = ChipUsage::where('user_id', $user->id)
            ->where('chip_type', 'triple_captain')
            ->exists();

        $freeHitActive = ChipUsage::where('user_id', $user->id)
            ->where('chip_type', 'free_hit')
            ->where('gameweek_id', $currentGameweek->id)
            ->exists();

        $hasUsedFreeHitBefore = ChipUsage::where('user_id', $user->id)
            ->where('chip_type', 'free_hit')
            ->exists();

        return view('my-team', compact(
            'team',
            'captain',
            'viceCaptain',
            'currentGameweek',
            'user',
            'displayTotalPoints',
            'teamGameweekPoints',
            'tripleCapUsed',
            'hasUsedTripleCapBefore',
            'freeHitActive',
            'hasUsedFreeHitBefore'
        ));
    }

    public function showBuilder()
    {
        $user = auth()->user();
        $userPatrolId = $user->patrol_id ?? ($user->scout ? $user->scout->patrol_id : null);
        $currentGameweek = Gameweek::where('is_current', true)->first();

        if (!$currentGameweek) {
            return redirect('/')->with('error', 'لا يوجد أسبوع نشط حالياً.');
        }

        if ($user->hasTeamForGameweek($currentGameweek->id)) {
            return redirect('/my-team')->with('info', 'لديك فريق بالفعل.');
        }

        $scouts = Scout::with('patrol')
            ->orderBy('current_price', 'desc')
            ->get();

        $patrols = \App\Models\Patrol::all();

        $scoutsPayload = $scouts->map(function ($scout) use ($user) {
            return [
                'scout_id' => $scout->scout_id,
                'first_name' => $scout->first_name,
                'last_name' => $scout->last_name,
                'full_name' => $scout->full_name,
                'patrol_id' => $scout->patrol_id,
                'role' => $scout->role,
                'photo_url' => $scout->photo_url,
                'current_price' => (float) $scout->current_price,
                'total_points' => (int) $scout->total_points,
                'form' => (float) $scout->form,
                'ownership_count' => (int) $scout->ownership_count,
                'local_ownership_count' => (int) $scout->local_ownership_count,
                'external_ownership_count' => (int) $scout->external_ownership_count,
                'is_available' => (bool) $scout->is_available,
                'can_pick' => $scout->canBeOwnedBy($user),
                'patrol' => $scout->patrol ? [
                    'patrol_name' => $scout->patrol->patrol_name,
                ] : null,
                'is_local_match' => $scout->isLocalOwner($user),
            ];
        })->values();

        return view('team-builder', compact('scoutsPayload', 'patrols', 'user', 'currentGameweek', 'userPatrolId'));
    }

    public function selectTeam(Request $request)
    {
        $user = auth()->user();
        $currentGameweek = Gameweek::where('is_current', true)->first();

        if (!$currentGameweek) {
            return response()->json(['error' => 'لا يوجد أسبوع نشط حالياً.'], 400);
        }

        if ($user->hasTeamForGameweek($currentGameweek->id)) {
            return response()->json(['error' => 'لديك فريق بالفعل.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'scouts' => 'required|array|size:11',
            'scouts.*' => 'required|exists:scouts,scout_id',
            'captain_id' => 'required|in:' . implode(',', $request->scouts ?? []),
            'vice_captain_id' => 'required|in:' . implode(',', $request->scouts ?? []),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validation = $this->validateTeamSelection($request->scouts, $request->captain_id, $request->vice_captain_id, $user);

        if (!$validation['valid']) {
            return response()->json(['error' => $validation['message']], 400);
        }

        DB::beginTransaction();
        try {
            $totalCost = 0;
            $position = 0;

            $totalUsers = User::where('role', 'user')->count();

            foreach ($request->scouts as $scoutId) {
                $scout = Scout::find($scoutId);

                UserTeam::create([
                    'user_id' => $user->id,
                    'gameweek_id' => $currentGameweek->id,
                    'scout_id' => $scoutId,
                    'position_in_squad' => $position,
                    'is_captain' => $scoutId === $request->captain_id,
                    'is_vice_captain' => $scoutId === $request->vice_captain_id,
                    'purchase_price' => $scout->current_price,
                    'current_price' => $scout->current_price,
                ]);

                $scout->incrementOwnershipFor($user);
                $scout->update([
                    'ownership_percentage' => ($scout->ownership_count / max($totalUsers, 1)) * 100,
                ]);

                $totalCost += $scout->current_price;
                $position++;
            }

            $user->update([
                'bank_balance' => 100.0 - $totalCost,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الفريق بنجاح!',
                'redirect' => '/my-team',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }

    public function changeCaptain(Request $request)
    {
        $request->validate([
            'captain_id' => 'required|exists:scouts,scout_id',
            'vice_captain_id' => 'required|exists:scouts,scout_id|different:captain_id',
        ]);

        $user = auth()->user();
        $currentGameweek = Gameweek::where('is_current', true)->first();

        if (!$currentGameweek || $currentGameweek->isDeadlinePassed()) {
            return back()->with('error', 'لا يمكن تغيير القائد بعد إغلاق الجولة.');
        }

        $team = UserTeam::where('user_id', $user->id)
            ->where('gameweek_id', $currentGameweek->id)
            ->get();

        $captainInTeam = $team->pluck('scout_id')->contains($request->captain_id);
        $viceInTeam = $team->pluck('scout_id')->contains($request->vice_captain_id);

        if (!$captainInTeam || !$viceInTeam) {
            return back()->with('error', 'القائد أو نائب القائد يجب أن يكون ضمن الفريق.');
        }

        UserTeam::where('user_id', $user->id)
            ->where('gameweek_id', $currentGameweek->id)
            ->update([
                'is_captain' => false,
                'is_vice_captain' => false,
            ]);

        UserTeam::where('user_id', $user->id)
            ->where('gameweek_id', $currentGameweek->id)
            ->where('scout_id', $request->captain_id)
            ->update(['is_captain' => true]);

        UserTeam::where('user_id', $user->id)
            ->where('gameweek_id', $currentGameweek->id)
            ->where('scout_id', $request->vice_captain_id)
            ->update(['is_vice_captain' => true]);

        return back()->with('success', 'تم تحديث القائد ونائب القائد.');
    }

    public function activateTripleCaptain(Request $request)
    {
        $user = auth()->user();
        $currentGameweek = Gameweek::where('is_current', true)->first();

        if (!$currentGameweek) {
            return back()->with('error', 'لا يوجد أسبوع نشط حالياً.');
        }

        if ($currentGameweek->isDeadlinePassed()) {
            return back()->with('error', 'لا يمكن تفعيل Triple Captain بعد إغلاق الجولة.');
        }

        // التحقق من أن المستخدم لم يستخدم Triple Captain من قبل
        $hasUsedBefore = ChipUsage::where('user_id', $user->id)
            ->where('chip_type', 'triple_captain')
            ->exists();

        if ($hasUsedBefore) {
            return back()->with('error', 'لقد استخدمت Triple Captain من قبل.');
        }

        // التحقق من أن لديه كابتن في الفريق
        $hasCaptain = UserTeam::where('user_id', $user->id)
            ->where('gameweek_id', $currentGameweek->id)
            ->where('is_captain', true)
            ->exists();

        if (!$hasCaptain) {
            return back()->with('error', 'يجب أن يكون لديك قائد في الفريق أولاً.');
        }

        // تفعيل Triple Captain
        ChipUsage::create([
            'user_id' => $user->id,
            'gameweek_id' => $currentGameweek->id,
            'chip_type' => 'triple_captain',
            'used_at' => now(),
        ]);

        return back()->with('success', 'تم تفعيل Triple Captain بنجاح! قائدك سيحصل على ×3 نقاط هذه الجولة ⚡');
    }

    public function activateFreeHit(Request $request)
    {
        $user = auth()->user();
        $currentGameweek = Gameweek::where('is_current', true)->first();

        if (!$currentGameweek) {
            return back()->with('error', 'لا يوجد أسبوع نشط حالياً.');
        }

        if ($currentGameweek->isDeadlinePassed()) {
            return back()->with('error', 'لا يمكن تفعيل Free Hit بعد إغلاق الجولة.');
        }

        // التحقق من أن المستخدم لم يستخدم Free Hit من قبل
        $hasUsedBefore = ChipUsage::where('user_id', $user->id)
            ->where('chip_type', 'free_hit')
            ->exists();

        if ($hasUsedBefore) {
            return back()->with('error', 'لقد استخدمت Free Hit من قبل.');
        }

        // تفعيل Free Hit
        ChipUsage::create([
            'user_id' => $user->id,
            'gameweek_id' => $currentGameweek->id,
            'chip_type' => 'free_hit',
            'used_at' => now(),
        ]);

        $this->saveOriginalTeamForFreeHit($user->id, $currentGameweek->id);
        $user->update(['free_hit_used' => true]);

        return back()->with('success', 'تم تفعيل Free Hit بنجاح! يمكنك الآن عمل تبديلات غير محدودة هذه الجولة 🔄');
    }

    private function saveOriginalTeamForFreeHit(int $userId, int $gameweekId): void
    {
        FreeHitSnapshot::where('user_id', $userId)
            ->where('gameweek_id', $gameweekId)
            ->delete();

        // حفظ التشكيلة الحالية "الآن" (لحظة الضغط) كنسخة احتياطية
        $currentTeam = UserTeam::where('user_id', $userId)
            ->where('gameweek_id', $gameweekId)
            ->get();

        // لو لسه معملش فريق للجولة دي، هات آخر فريق من الجولة اللي فاتت
        if ($currentTeam->isEmpty()) {
            $currentTeam = UserTeam::where('user_id', $userId)
                ->orderBy('gameweek_id', 'desc')
                ->limit(11)
                ->get();
        }

        foreach ($currentTeam as $player) {
            FreeHitSnapshot::create([
                'user_id' => $userId,
                'gameweek_id' => $gameweekId,
                'scout_id' => $player->scout_id,
                'position_in_squad' => $player->position_in_squad,
                'is_captain' => $player->is_captain,
                'is_vice_captain' => $player->is_vice_captain,
                'purchase_price' => $player->purchase_price ?? $player->current_price,
                'current_price' => $player->current_price,
            ]);
        }
    }

    private function validateTeamSelection($scoutIds, $captainId, $viceCaptainId, $user)
    {
        if (count($scoutIds) !== 11) {
            return ['valid' => false, 'message' => 'يجب اختيار 11 كشاف بالضبط.'];
        }

        if (count(array_unique($scoutIds)) !== 11) {
            return ['valid' => false, 'message' => 'لا يمكن اختيار نفس الكشاف أكثر من مرة.'];
        }

        if (in_array($user->scout_id, $scoutIds)) {
            return ['valid' => false, 'message' => 'لا يمكنك اختيار نفسك ضمن الفريق.'];
        }

        if ($captainId === $viceCaptainId) {
            return ['valid' => false, 'message' => 'لا يمكن أن يكون القائد ونائب القائد نفس الشخص.'];
        }

        $scouts = Scout::whereIn('scout_id', $scoutIds)->get();

        if ($scouts->count() !== 11) {
            return ['valid' => false, 'message' => 'هناك كشاف غير صالح في الاختيار.'];
        }

        $totalCost = $scouts->sum('current_price');
        if ($totalCost > 100.0) {
            return ['valid' => false, 'message' => "تجاوزت الميزانية المتاحة: {$totalCost}"]; 
        }

        foreach ($scouts as $scout) {
            if (!$scout->canBeOwnedBy($user)) {
                return ['valid' => false, 'message' => "{$scout->full_name} غير متاح (الحد اكتمل)."];
            }
        }

        $leadersCount = $scouts->whereIn('role', ['leader', 'senior'])->count();
        if ($leadersCount !== 3) {
            return ['valid' => false, 'message' => "Select exactly 3 leaders (currently {$leadersCount})."]; 
        }

        $nonLeaders = $scouts->whereNotIn('role', ['leader', 'senior']);
        if ($nonLeaders->count() !== 8) {
            return ['valid' => false, 'message' => "Select exactly 8 non-leaders (currently {$nonLeaders->count()})."]; 
        }

        $userPatrolId = $user->patrol_id ?? ($user->scout ? $user->scout->patrol_id : null);
        if (!$userPatrolId) {
            return ['valid' => false, 'message' => 'User patrol not set.'];
        }

        $ownPatrolCount = $nonLeaders->where('patrol_id', $userPatrolId)->count();
        if ($ownPatrolCount !== 4) {
            return ['valid' => false, 'message' => "Select 4 from your patrol (non-leaders only) (currently {$ownPatrolCount})."]; 
        }

        $otherPatrolCount = $nonLeaders->where('patrol_id', '!=', $userPatrolId)->count();
        if ($otherPatrolCount !== 4) {
            return ['valid' => false, 'message' => "Select 4 from other patrols (non-leaders only) (currently {$otherPatrolCount})."]; 
        }
        return ['valid' => true];
    }

    private function ensureTeamForCurrentGameweek(User $user, Gameweek $gameweek): void
    {
        $latestTeam = UserTeam::where('user_id', $user->id)
            ->orderBy('gameweek_id', 'desc')
            ->first();

        if (!$latestTeam) {
            return;
        }

        $sourceGameweekId = $latestTeam->gameweek_id;
        if ($sourceGameweekId === $gameweek->id) {
            return;
        }

        $sourceTeam = UserTeam::where('user_id', $user->id)
            ->where('gameweek_id', $sourceGameweekId)
            ->get();

        if ($sourceTeam->isEmpty()) {
            return;
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


