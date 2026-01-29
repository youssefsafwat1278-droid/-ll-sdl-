<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patrol extends Model
{
    use HasFactory;

    protected $primaryKey = 'patrol_id';

    protected $fillable = [
        'patrol_name',
        'patrol_logo_url',
        'patrol_color',
        'total_points',
        'rank',
        'description',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'rank' => 'integer',
    ];

    public function scouts()
    {
        return $this->hasMany(Scout::class, 'patrol_id', 'patrol_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'patrol_id', 'patrol_id');
    }

    public function rankings()
    {
        return $this->hasMany(PatrolRanking::class, 'patrol_id', 'patrol_id');
    }

    public function gameweekPoints()
    {
        return $this->hasMany(PatrolGameweekPoint::class, 'patrol_id', 'patrol_id');
    }

    public function scopeRanked(Builder $query): Builder
    {
        return $query->orderByRaw('rank IS NULL, rank ASC');
    }

    public function scopeTopPerformers(Builder $query, int $limit = 5): Builder
    {
        return $query->orderBy('total_points', 'desc')->limit($limit);
    }

    public function getAveragePointsAttribute(): float
    {
        $scoutCount = $this->scouts()->count();
        return $scoutCount > 0 ? round($this->total_points / $scoutCount, 1) : 0.0;
    }

    public function getCurrentGameweekPointsAttribute(): int
    {
        $currentGameweek = Gameweek::query()->where('is_current', true)->first();
        if (!$currentGameweek) return 0;

        $ranking = $this->rankings()
            ->where('gameweek_id', $currentGameweek->id)
            ->first();

        return $ranking ? (int) $ranking->gameweek_points : 0;
    }
}