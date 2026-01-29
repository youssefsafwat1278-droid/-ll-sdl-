<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'scout_id',
        'email',
        'password',
        'first_name',
        'last_name',
        'patrol_id',
        'photo_url',
        'team_name',
        'bank_balance',
        'total_points',
        'gameweek_points',
        'free_transfers',
        'triple_captain_used',
        'bench_boost_used',
        'free_hit_used',
        'theme',
        'language',
        'notifications_enabled',
        'profile_public',
        'role',
        'last_login',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email' => 'string',
        'patrol_id' => 'integer',
        'bank_balance' => 'decimal:1',
        'total_points' => 'integer',
        'gameweek_points' => 'integer',
        'free_transfers' => 'integer',
        'triple_captain_used' => 'integer',
        'bench_boost_used' => 'boolean',
        'free_hit_used' => 'boolean',
        'notifications_enabled' => 'boolean',
        'profile_public' => 'boolean',
        'last_login' => 'datetime',
    ];

    // Relationships
    public function scout()
    {
        return $this->belongsTo(Scout::class, 'scout_id', 'scout_id');
    }

    public function patrol()
    {
        return $this->belongsTo(Patrol::class, 'patrol_id', 'patrol_id');
    }

    public function teams()
    {
        return $this->hasMany(UserTeam::class, 'user_id', 'id');
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'user_id', 'id');
    }

    public function chipUsages()
    {
        return $this->hasMany(ChipUsage::class, 'user_id', 'id');
    }

    public function rankings()
    {
        return $this->hasMany(OverallRanking::class, 'user_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id');
    }

    public function newsArticles()
    {
        return $this->hasMany(News::class, 'author_id', 'id');
    }

    public function gameweekPoints()
    {
        return $this->hasMany(UserGameweekPoint::class, 'user_id', 'id');
    }

    public function teamMemberPoints()
    {
        return $this->hasMany(TeamMemberPoint::class, 'user_id', 'id');
    }

    // Scopes
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    public function scopeUsersOnly(Builder $query): Builder
    {
        return $query->where('role', 'user');
    }

    public function scopeTop(Builder $query, int $limit = 10): Builder
    {
        return $query->orderByDesc('total_points')->limit($limit);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
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

    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    public function setPasswordAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = $this->isBcryptHash($value)
            ? $value
            : Hash::make($value);
    }

    private function isBcryptHash(string $value): bool
    {
        return str_starts_with($value, '$2y$')
            || str_starts_with($value, '$2a$')
            || str_starts_with($value, '$2b$');
    }

    // Helpers
    public function currentGameweek(): ?Gameweek
    {
        return Gameweek::query()->where('is_current', true)->first();
    }

    public function getCurrentTeam()
    {
        $gw = $this->currentGameweek();
        if (!$gw) return collect();

        return $this->teams()
            ->with(['scout.patrol'])
            ->where('gameweek_id', $gw->id)
            ->orderBy('position_in_squad')
            ->get();
    }

    public function hasTeamForGameweek(int $gameweekId): bool
    {
        return $this->teams()->where('gameweek_id', $gameweekId)->exists();
    }

    public function canUseTripleCaptain(): bool
    {
        return (int)$this->triple_captain_used < 3;
    }

    public function canUseBenchBoost(): bool
    {
        return !$this->bench_boost_used;
    }

    public function canUseFreeHit(): bool
    {
        return !$this->free_hit_used;
    }
}
