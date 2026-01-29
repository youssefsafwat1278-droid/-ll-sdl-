<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gameweek_id',
        'scout_id',
        'position_in_squad',
        'is_captain',
        'is_vice_captain',
        'purchase_price',
        'current_price',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'gameweek_id' => 'integer',
        'position_in_squad' => 'integer',
        'is_captain' => 'boolean',
        'is_vice_captain' => 'boolean',
        'purchase_price' => 'decimal:1',
        'current_price' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function gameweek()
    {
        return $this->belongsTo(Gameweek::class, 'gameweek_id', 'id');
    }

    public function scout()
    {
        return $this->belongsTo(Scout::class, 'scout_id', 'scout_id');
    }

    // Scopes
    public function scopeForGameweek(Builder $query, int $gameweekId): Builder
    {
        return $query->where('gameweek_id', $gameweekId);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
