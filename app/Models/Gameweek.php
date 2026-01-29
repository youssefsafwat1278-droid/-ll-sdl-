<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gameweek extends Model
{
    use HasFactory;

    protected $fillable = [
        'gameweek_number',
        'name',
        'date',
        'location',
        'photo_url',
        'description',
        'deadline',
        'is_current',
        'is_finished',
    ];

    protected $casts = [
        'gameweek_number' => 'integer',
        'date' => 'date',
        'deadline' => 'datetime',
        'is_current' => 'boolean',
        'is_finished' => 'boolean',
    ];

    // Relationships
    public function userTeams()
    {
        return $this->hasMany(UserTeam::class, 'gameweek_id', 'id');
    }

    public function performances()
    {
        return $this->hasMany(ScoutGameweekPerformance::class, 'gameweek_id', 'id');
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'gameweek_id', 'id');
    }

    public function chipUsages()
    {
        return $this->hasMany(ChipUsage::class, 'gameweek_id', 'id');
    }

    public function overallRankings()
    {
        return $this->hasMany(OverallRanking::class, 'gameweek_id', 'id');
    }

    public function patrolRankings()
    {
        return $this->hasMany(PatrolRanking::class, 'gameweek_id', 'id');
    }

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class, 'gameweek_id', 'id');
    }

    // Scopes
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('is_finished', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date', '>=', now()->toDateString())->orderBy('date');
    }

    // Helpers
    public function isDeadlinePassed(): bool
    {
        return Carbon::now()->isAfter($this->deadline);
    }

    public function hoursUntilDeadline(): int
    {
        return Carbon::now()->diffInHours($this->deadline, false);
    }

    public function getFormattedDeadlineAttribute(): string
    {
        return $this->deadline ? $this->deadline->format('Y-m-d H:i:s') : '';
    }
}
