<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scout extends Model
{
    use HasFactory;

    protected $primaryKey = 'scout_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'scout_id',
        'first_name',
        'last_name',
        'patrol_id',
        'role',
        'photo_url',
        'initial_price',
        'current_price',
        'price_change',
        'price_trend',
        'total_points',
        'gameweek_points',
        'form',
        'ownership_count',
        'local_ownership_count',
        'external_ownership_count',
        'previous_ownership_count',
        'ownership_percentage',
        'ownership_average',
        'is_available',
        'status',
    ];

    protected $casts = [
        'patrol_id' => 'integer',
        'initial_price' => 'decimal:1',
        'current_price' => 'decimal:1',
        'price_change' => 'decimal:1',
        'total_points' => 'integer',
        'gameweek_points' => 'integer',
        'form' => 'decimal:1',
        'ownership_count' => 'integer',
        'local_ownership_count' => 'integer',
        'external_ownership_count' => 'integer',
        'previous_ownership_count' => 'integer',
        'ownership_percentage' => 'decimal:2',
        'ownership_average' => 'decimal:1',
        'is_available' => 'boolean',
    ];

    public function patrol()
    {
        return $this->belongsTo(Patrol::class, 'patrol_id', 'patrol_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'scout_id', 'scout_id');
    }

    public function performances()
    {
        return $this->hasMany(ScoutGameweekPerformance::class, 'scout_id', 'scout_id');
    }

    public function userTeams()
    {
        return $this->hasMany(UserTeam::class, 'scout_id', 'scout_id');
    }

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class, 'scout_id', 'scout_id');
    }

    public function transfersOut()
    {
        return $this->hasMany(Transfer::class, 'scout_out_id', 'scout_id');
    }

    public function transfersIn()
    {
        return $this->hasMany(Transfer::class, 'scout_in_id', 'scout_id');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    public function scopeByPatrol(Builder $query, int $patrolId): Builder
    {
        return $query->where('patrol_id', $patrolId);
    }

    public function scopeByRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    public function scopeLeadersOrSeniors(Builder $query): Builder
    {
        return $query->whereIn('role', ['leader', 'senior']);
    }

    public function scopePriceRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('current_price', [$min, $max]);
    }

    public function scopeTopPerformers(Builder $query, int $limit = 10): Builder
    {
        return $query->orderBy('total_points', 'desc')->limit($limit);
    }

    public function scopeTrending(Builder $query): Builder
    {
        return $query->where('price_trend', 'rising')
            ->orderByDesc('price_change');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getPhotoUrlAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        if (preg_match('/^(https?:)?\\/\\//i', $value) || str_starts_with($value, 'data:')) {
            return $value;
        }

        return asset(ltrim($value, '/'));
    }

    public function getPriceChangeIconAttribute(): string
    {
        if ((float)$this->price_change > 0) return '↑';
        if ((float)$this->price_change < 0) return '↓';
        return '→';
    }

    public function getOwnershipStatusAttribute(): string
    {
        return (int)$this->ownership_count . '/5';
    }

    public function isLeaderOrSenior(): bool
    {
        return in_array($this->role, ['leader', 'senior'], true);
    }

    public function isLocalOwner(User $user): bool
    {
        return $user->patrol_id
            && $this->patrol_id
            && (int) $user->patrol_id === (int) $this->patrol_id;
    }

    public function ownershipLimitFor(User $user): int
    {
        if ($this->isLeaderOrSenior()) {
            return 20;
        }

        return $this->isLocalOwner($user) ? 7 : 5;
    }

    public function canBeOwnedBy(User $user, bool $ignoreLimits = false): bool
    {
        if ($ignoreLimits) {
            return true;
        }

        if ($this->isLeaderOrSenior()) {
            return (int) $this->ownership_count < 20;
        }

        if ($this->isLocalOwner($user)) {
            return (int) $this->local_ownership_count < 7;
        }

        return (int) $this->external_ownership_count < 5;
    }

    public function incrementOwnershipFor(User $user): void
    {
        $this->ownership_count = (int) $this->ownership_count + 1;

        if (!$this->isLeaderOrSenior()) {
            if ($this->isLocalOwner($user)) {
                $this->local_ownership_count = (int) $this->local_ownership_count + 1;
            } else {
                $this->external_ownership_count = (int) $this->external_ownership_count + 1;
            }
        }

        $this->refreshAvailability();
        $this->save();
    }

    public function decrementOwnershipFor(User $user): void
    {
        $this->ownership_count = max(0, (int) $this->ownership_count - 1);

        if (!$this->isLeaderOrSenior()) {
            if ($this->isLocalOwner($user)) {
                $this->local_ownership_count = max(0, (int) $this->local_ownership_count - 1);
            } else {
                $this->external_ownership_count = max(0, (int) $this->external_ownership_count - 1);
            }
        }

        $this->refreshAvailability();
        $this->save();
    }

    public function refreshAvailability(): void
    {
        if ($this->isLeaderOrSenior()) {
            $this->is_available = (int) $this->ownership_count < 20;
            return;
        }

        $this->is_available = (int) $this->local_ownership_count < 7
            || (int) $this->external_ownership_count < 5;
    }

    public function calculateForm(): float
    {
        $lastFive = $this->performances()
            ->latest('gameweek_id')
            ->limit(5)
            ->pluck('total_points');

        return $lastFive->isNotEmpty() ? round($lastFive->average(), 1) : 0.0;
    }

    public function updatePrice(float $newPrice, int $gameweekId, ?string $reason = null): void
    {
        $oldPrice = (float) $this->current_price;
        $newPrice = round($newPrice, 1);
        $change = round($newPrice - $oldPrice, 1);

        if ($change == 0.0) return;

        PriceHistory::create([
            'scout_id' => $this->scout_id,
            'gameweek_id' => $gameweekId,
            'price_before' => $oldPrice,
            'price_after' => $newPrice,
            'price_change' => $change,
            'ownership_count' => (int) $this->ownership_count,
            'previous_ownership_count' => (int) $this->previous_ownership_count,
            'ownership_average' => (float) $this->ownership_average,
            'reason' => $reason,
        ]);

        $this->update([
            'current_price' => $newPrice,
            'price_change' => $change,
            'price_trend' => $change > 0 ? 'rising' : 'falling',
        ]);
    }
}
