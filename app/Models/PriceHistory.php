<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'scout_id',
        'gameweek_id',
        'price_before',
        'price_after',
        'price_change',
        'ownership_count',
        'previous_ownership_count',
        'ownership_average',
        'reason',
    ];

    protected $casts = [
        'gameweek_id' => 'integer',
        'price_before' => 'decimal:1',
        'price_after' => 'decimal:1',
        'price_change' => 'decimal:1',
        'ownership_count' => 'integer',
        'previous_ownership_count' => 'integer',
        'ownership_average' => 'decimal:1',
    ];

    public function scout()
    {
        return $this->belongsTo(Scout::class, 'scout_id', 'scout_id');
    }

    public function gameweek()
    {
        return $this->belongsTo(Gameweek::class, 'gameweek_id', 'id');
    }
}
