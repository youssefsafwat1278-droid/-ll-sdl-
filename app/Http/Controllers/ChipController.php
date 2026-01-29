<?php

namespace App\Http\Controllers;

use App\Models\Gameweek;
use App\Models\ChipUsage;
use App\Models\FreeHitSnapshot;
use App\Models\UserTeam;
use Illuminate\Http\Request;

class ChipController extends Controller
{
    public function use(Request $request)
    {
        $request->validate([
            'chip_type' => 'required|in:triple_captain,bench_boost,free_hit',
        ]);

        $user = auth()->user();
        $currentGameweek = Gameweek::where('is_current', true)->first();

        if (!$currentGameweek) {
            return back()->with('error', 'لا يوجد أسبوع نشط');
        }

        if ($currentGameweek->isDeadlinePassed()) {
            return back()->with('error', 'انتهى الموعد النهائي');
        }

        $chipType = $request->chip_type;

        $alreadyUsed = ChipUsage::where('user_id', $user->id)
            ->where('gameweek_id', $currentGameweek->id)
            ->exists();

        if ($alreadyUsed) {
            return back()->with('error', 'استخدمت بطاقة بالفعل هذا الأسبوع');
        }

        if ($chipType === 'triple_captain' && !$user->canUseTripleCaptain()) {
            return back()->with('error', 'استخدمت القائد الثلاثي 3 مرات بالفعل');
        }

        if ($chipType === 'bench_boost' && !$user->canUseBenchBoost()) {
            return back()->with('error', 'استخدمت بطاقة البدلاء بالفعل');
        }

        if ($chipType === 'free_hit' && !$user->canUseFreeHit()) {
            return back()->with('error', 'استخدمت الضربة الحرة بالفعل');
        }

        ChipUsage::create([
            'user_id' => $user->id,
            'gameweek_id' => $currentGameweek->id,
            'chip_type' => $chipType,
        ]);

        if ($chipType === 'triple_captain') {
            $user->increment('triple_captain_used');
        } elseif ($chipType === 'bench_boost') {
            $user->update(['bench_boost_used' => true]);
        } elseif ($chipType === 'free_hit') {
            // حفظ الفريق الأصلي قبل تفعيل Free Hit
            $this->saveOriginalTeam($user->id, $currentGameweek->id);
            $user->update(['free_hit_used' => true]);
        }

        $chipNames = [
            'triple_captain' => 'القائد الثلاثي',
            'bench_boost' => 'بطاقة البدلاء',
            'free_hit' => 'الضربة الحرة',
        ];

        return back()->with('success', "تم تفعيل {$chipNames[$chipType]} بنجاح!");
    }

    /**
     * حفظ الفريق الأصلي قبل تفعيل Free Hit
     */
    private function saveOriginalTeam(int $userId, int $gameweekId): void
    {
        // حذف أي نسخة احتياطية سابقة لنفس المستخدم والجولة
        FreeHitSnapshot::where('user_id', $userId)
            ->where('gameweek_id', $gameweekId)
            ->delete();

        // حفظ الفريق الحالي
        $currentTeam = UserTeam::where('user_id', $userId)
            ->where('gameweek_id', $gameweekId)
            ->get();

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
}
