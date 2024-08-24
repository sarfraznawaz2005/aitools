<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tip extends Model
{
    use HasFactory;

    protected $fillable = ['api_key_id', 'name', 'prompt', 'cron', 'active'];

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(TipContent::class);
    }
}
