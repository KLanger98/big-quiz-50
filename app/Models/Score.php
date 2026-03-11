<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    protected $fillable = [
        'user_id',
        'week_id',
        'score',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class);
    }
}
