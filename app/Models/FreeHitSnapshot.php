<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeHitSnapshot extends Model
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
        'is_captain' => 'boolean',
        'is_vice_captain' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gameweek()
    {
        return $this->belongsTo(Gameweek::class);
    }

    public function scout()
    {
        return $this->belongsTo(Scout::class, 'scout_id', 'scout_id');
    }
}
