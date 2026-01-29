<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gameweek_id',
        'scout_out_id',
        'scout_in_id',
        'price_out',
        'price_in',
        'transfer_cost',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'gameweek_id' => 'integer',
        'price_out' => 'decimal:1',
        'price_in' => 'decimal:1',
        'transfer_cost' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function gameweek()
    {
        return $this->belongsTo(Gameweek::class, 'gameweek_id', 'id');
    }

    public function scoutOut()
    {
        return $this->belongsTo(Scout::class, 'scout_out_id', 'scout_id');
    }

    public function scoutIn()
    {
        return $this->belongsTo(Scout::class, 'scout_in_id', 'scout_id');
    }

    // Scopes
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForGameweek(Builder $query, int $gameweekId): Builder
    {
        return $query->where('gameweek_id', $gameweekId);
    }
}
