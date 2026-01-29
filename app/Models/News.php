<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'image_url', 'author_id', 'is_featured'];

    protected $casts = [
        'author_id' => 'integer',
        'is_featured' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
