<?php

namespace App\Models;

use App\Constants;
use App\Enums\BotTypeEnum;
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
        'related_questions',
        'system',
    ];

    public function isDocumentBot(): bool
    {
        return $this->type === BotTypeEnum::DOCUMENT->value;
    }

    public function showRelatedQuestions(): bool
    {
        if (Constants::RELATED_QUESTIONS_ENABLED) {
            return (bool)$this->related_questions;
        }

        return false;
    }

    public function files(): array
    {
        return glob(storage_path('app/files/') . strtolower(Str::slug($this->name)) . '/*');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class)->chaperone('conversation');
    }
}
