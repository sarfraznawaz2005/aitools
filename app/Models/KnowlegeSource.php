<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowlegeSource extends Model
{
    public $fillable = [
        'bot_id',
        'path',
    ];

    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }
}
