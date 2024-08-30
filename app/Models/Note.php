<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Native\Laravel\Facades\Settings;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_folder_id',
        'title',
        'content',
        'archived',
        'favorite',
        'reminder_at',
        'code',
        'url',
        'image',
        'author',
        'author_url',
        'source',
        'source_url',
        'source_icon',
        'published_at',
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleteOld();
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo(NoteFolder::class);
    }

    public static function deleteOld(): void
    {
        $days = Settings::get('Notes.deleteOldDays', 30);

        $oldItems = static::query()
            ->where('created_at', '<', now()->subDays($days))
            ->where('favorite', false)
            ->where('archived', false);

        if ($oldItems->exists()) {
            $deletedCount = $oldItems->delete();

            Log::info("Deleted $deletedCount old notes.");
        }
    }
}
