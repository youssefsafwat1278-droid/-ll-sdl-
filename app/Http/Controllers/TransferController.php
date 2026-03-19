<?php
// app/Http/Controllers/TransferController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Scout;
use App\Models\Gameweek;
use App\Models\UserTeam;
use App\Models\Transfer;
use App\Models\ChipUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('scout.patrol');
        $currentGameweek = Gameweek::where('is_current', true)->first();

        if (!$currentGameweek) {
            return view('transfers', [
                'user' => $user,
                'currentGameweek' => null,
                'deadlinePassed' => true,
                'hasGameweek' => false,
                'message' => 'لا يوجد أسبوع نشط حالياً.',
                'currentTeam' => collect(),
                'currentTeamScouts' => collect(),
                'availableScouts' => collect(),
                'patrols' => collect(),
                'freeHitActive' => false,
            ]);
        }

        $deadlinePassed = $currentGameweek->isDeadlinePassed();

        $currentTeam = $user->teams()
            ->with('scout.patrol')
            ->where('gameweek_id', $currentGameweek->id)
            ->orderBy('position_in_squad')
            ->get();

        $freeHitActive = ChipUsage::where('user_id', $user->id)
            ->where('gameweek_id', $currentGameweek->id)
            ->where('chip_type', 'free_hit')
            ->exists();

        $scouts = Scout::with('patrol')
            ->orderBy('total_points', 'desc')
            ->get();

        $availableScouts = $scouts->map(function ($scout) use ($user, $freeHitActive) {
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
                'can_pick' => $freeHitActive ? true : $scout->canBeOwnedBy($user),
                'patrol' => $scout->patrol ? [
                    'patrol_name' => $scout->patrol->patrol_name,
                ] : null,
                'is_local_match' => $scout->isLocalOwner($user),
            ];
        })->values();

        // تحويل currentTeam إلى نفس الصيغة المستخدمة في availableScouts
        $currentTeamScouts = $currentTeam->map(function ($member) {
            return [
                'scout_id' => $member->scout->scout_id,
                'first_name' => $member->scout->first_name,
                'last_name' => $member->scout->last_name,
                'full_name' => $member->scout->full_name,
                'patrol_id' => $member->scout->patrol_id,
                'role' => $member->scout->role,
                'photo_url' => $member->scout->photo_url,
                'current_price' => (float) $member->scout->current_price,
                'total_points' => (int) $member->scout->total_points,
                'form' => (float) ($member->scout->form ?? 0),
                'ownership_count' => (int) $member->scout->ownership_count,
                'is_available' => (bool) $member->scout->is_available,
                'patrol' => $member->scout->patrol ? [
                    'patrol_name' => $member->scout->patrol->patrol_name,
                ] : null,
            ];
        })->values();

        $patrols = \App\Models\Patrol::orderBy('patrol_name')->get();

        return view('transfers', [
            'user' => $user,
            'currentGameweek' => $currentGameweek,
            'deadlinePassed' => $deadlinePassed,
            'hasGameweek' => true,
            'currentTeam' => $currentTeam,
            'currentTeamScouts' => $currentTeamScouts,
            'availableScouts' => $availableScouts,
            'patrols' => $patrols,
            'freeHitActive' => $freeHitActive,
        ]);
    }

    public function makeTransfers(Request $request)
    {
        $user = auth()->user()->load('scout.patrol');
        $currentGameweek = Gameweek::where('is_current', true)->first();

        if (!$currentGameweek) {
            return response()->json(['error' => 'لا يوجد أسبوع نشط حالياً.'], 400);
        }

        if ($currentGameweek->isDeadlinePassed()) {
            return response()->json(['error' => 'تم إغلاق الانتقالات بعد الموعد النهائي.'], 400);
        }

        $request->validate([
            'transfers' => 'required|array|min:1|max:11',
            'transfers.*.scout_out' => 'required|exists:scouts,scout_id',
            'transfers.*.scout_in' => 'required|exists:scouts,scout_id',
        ]);

        $freeHitActive = ChipUsage::where('user_id', $user->id)
            ->where('gameweek_id', $currentGameweek->id)
            ->where('chip_type', 'free_hit')
            ->exists();

        $validation = $this->validateTransfers($request->transfers, $user, $currentGameweek, $freeHitActive);

        if (!$validation['valid']) {
            return response()->json(['error' => $validation['message']], 400);
        }

        DB::beginTransaction();
        try {
            $transferCount = count($request->transfers);
            $transferCost = 0;

            // التحقق من تفعيل Free Hit في الجولة الحالية
            $freeHitActive = ChipUsage::where('user_id', $user->id)
                ->where('gameweek_id', $currentGameweek->id)
                ->where('chip_type', 'free_hit')
                ->exists();

            // إذا كان Free Hit مفعّل، لا يوجد penalty على التبديلات
            if (!$freeHitActive && $transferCount > $user->free_transfers) {
                $transferCost = ($transferCount - $user->free_transfers) * 4;
            }

            $moneyIn = 0;
            $moneyOut = 0;

            foreach ($request->transfers as $transfer) {
                $scoutOut = Scout::find($transfer['scout_out']);
                $scoutIn = Scout::find($transfer['scout_in']);

                $teamMember = UserTeam::where('user_id', $user->id)
                    ->where('gameweek_id', $currentGameweek->id)
                    ->where('scout_id', $transfer['scout_out'])
                    ->first();

                $isCaptain = $teamMember->is_captain;
                $isViceCaptain = $teamMember->is_vice_captain;
                $position = $teamMember->position_in_squad;

                $teamMember->delete();

                UserTeam::create([
                    'user_id' => $user->id,
                    'gameweek_id' => $currentGameweek->id,
                    'scout_id' => $transfer['scout_in'],
                    'position_in_squad' => $position,
                    'is_captain' => $isCaptain,
                    'is_vice_captain' => $isViceCaptain,
                    'purchase_price' => $scoutIn->current_price,
                    'current_price' => $scoutIn->current_price,
                ]);

                Transfer::create([
                    'user_id' => $user->id,
                    'gameweek_id' => $currentGameweek->id,
                    'scout_out_id' => $transfer['scout_out'],
                    'scout_in_id' => $transfer['scout_in'],
                    'price_out' => $scoutOut->current_price,
                    'price_in' => $scoutIn->current_price,
                    'transfer_cost' => $transferCost / $transferCount,
                ]);

                // تحديث ownership counts (لا يتم أثناء Free Hit لأنه مؤقت)
                if (!$freeHitActive) {
                    $scoutOut->decrementOwnershipFor($user);
                    $scoutOut->update([
                        'ownership_percentage' => ($scoutOut->ownership_count / User::where('role', 'user')->count()) * 100,
                    ]);

                    $scoutIn->incrementOwnershipFor($user);
                    $scoutIn->update([
                        'ownership_percentage' => ($scoutIn->ownership_count / User::where('role', 'user')->count()) * 100,
                    ]);
                }

                $moneyIn += $scoutOut->current_price;
                $moneyOut += $scoutIn->current_price;
            }

            $newBalance = $user->bank_balance + $moneyIn - $moneyOut;

            // تحديث رصيد المستخدم
            // إذا كان Free Hit مفعّل، لا نقلل free_transfers
            if ($freeHitActive) {
                $user->update([
                    'bank_balance' => $newBalance,
                ]);
            } else {
                $user->update([
                    'bank_balance' => $newBalance,
                    'free_transfers' => max(0, $user->free_transfers - $transferCount),
                ]);
            }

            DB::commit();

            $successMessage = "تم تنفيذ {$transferCount} تبديل بنجاح!";
            if ($freeHitActive) {
                $successMessage .= " (Free Hit مفعّل - لا توجد عقوبة 🔄)";
            } elseif ($transferCost > 0) {
                $successMessage .= " (خصم {$transferCost} نقطة)";
            }

            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'transfer_cost' => $transferCost,
                'new_balance' => $newBalance,
                'free_hit_active' => $freeHitActive,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }

    public function history()
    {
        $user = auth()->user();

        $transfers = Transfer::where('user_id', $user->id)
            ->with(['scoutOut.patrol', 'scoutIn.patrol', 'gameweek'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('transfers-history', compact('transfers'));
    }

    private function validateTransfers($transfers, $user, $gameweek, $freeHitActive = false)
    {
        $currentTeam = UserTeam::where('user_id', $user->id)
            ->where('gameweek_id', $gameweek->id)
            ->with('scout.patrol')
            ->get();

        $scoutsOut = collect($transfers)->pluck('scout_out')->toArray();
        $scoutsIn = collect($transfers)->pluck('scout_in')->toArray();

        if (count(array_unique($scoutsOut)) !== count($scoutsOut)) {
            return ['valid' => false, 'message' => 'لا يمكن اختيار نفس الكشاف للخروج أكثر من مرة.'];
        }

        if (count(array_unique($scoutsIn)) !== count($scoutsIn)) {
            return ['valid' => false, 'message' => 'لا يمكن اختيار نفس الكشاف للدخول أكثر من مرة.'];
        }

        foreach ($scoutsOut as $scoutId) {
            if (!$currentTeam->pluck('scout_id')->contains($scoutId)) {
                return ['valid' => false, 'message' => "الكشاف {$scoutId} ليس ضمن فريقك."];
            }
        }

        $remainingTeamMembers = $currentTeam->whereNotIn('scout_id', $scoutsOut);
        $newScouts = Scout::whereIn('scout_id', $scoutsIn)->get();

        // السماح باختيار اللاعبين المقفولين أثناء Free Hit
        if (!$freeHitActive) {
            foreach ($newScouts as $scout) {
                if (!$scout->canBeOwnedBy($user)) {
                    return ['valid' => false, 'message' => "{$scout->full_name} غير متاح حالياً (وصل للحد الأقصى)."];
                }
            }
        }

        $remainingScouts = $remainingTeamMembers->pluck('scout');
        $finalTeam = $remainingScouts->merge($newScouts);

        $totalCost = $newScouts->sum('current_price');
        $totalSale = Scout::whereIn('scout_id', $scoutsOut)->sum('current_price');
        $newBalance = $user->bank_balance + $totalSale - $totalCost;

        if ($newBalance < 0) {
            return ['valid' => false, 'message' => 'رصيدك لا يكفي لإجراء هذه التبديلات.'];
        }

        $userPatrolId = $user->patrol_id ?? ($user->scout ? $user->scout->patrol_id : null);

        if (!$userPatrolId) {
            return ['valid' => false, 'message' => 'لم يتم تحديد طليعتك بشكل صحيح.'];
        }

        $leadersCount = $finalTeam->whereIn('role', ['leader', 'senior'])->count();
        if ($leadersCount !== 3) {
            return ['valid' => false, 'message' => 'يجب أن تختار بالضبط 3 قادة/رواد.'];
        }

        $nonLeaders = $finalTeam->whereNotIn('role', ['leader', 'senior']);
        $ownPatrolCount = $nonLeaders->filter(function($scout) use ($userPatrolId) {
            return $scout->patrol_id == $userPatrolId;
        })->count();
        if ($ownPatrolCount !== 4) {
            return ['valid' => false, 'message' => 'يجب أن تختار بالضبط 4 كشافة من طليعتك (غير القادة/الرواد).'];
        }

        $otherPatrolCount = $nonLeaders->filter(function($scout) use ($userPatrolId) {
            return $scout->patrol_id != $userPatrolId;
        })->count();
        if ($otherPatrolCount !== 4) {
            return ['valid' => false, 'message' => 'يجب أن تختار بالضبط 4 كشافة من طلائع أخرى (غير القادة/الرواد).'];
        }

        return ['valid' => true];
    }
}
