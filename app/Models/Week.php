<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Week extends Model
{
    protected $fillable = [
        'competition_id',
        'week_number',
        'start_date',
        'end_date',
        'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_locked' => 'boolean',
        ];
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }
}
