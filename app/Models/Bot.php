<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Bot extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'bio',
        'prompt',
        'type',
        'icon',
    ];

    public function isDocumentBot(): bool
    {
        return count(glob(base_path('storage/app/files/') . strtolower(Str::slug($this->name)) . '/*')) > 0;
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
