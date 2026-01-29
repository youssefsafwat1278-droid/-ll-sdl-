<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGameweekPoint extends Model
{
    protected $fillable = [
        'user_id',
        'gameweek_id',
        'team_points',
        'transfer_penalty',
        'net_points',
        'total_points_after',
        'rank_in_gameweek',
    ];

    protected $casts = [
        'team_points' => 'integer',
        'transfer_penalty' => 'integer',
        'net_points' => 'integer',
        'total_points_after' => 'integer',
        'rank_in_gameweek' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gameweek()
    {
        return $this->belongsTo(Gameweek::class);
    }
}