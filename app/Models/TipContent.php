<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Native\Laravel\Facades\Settings;

class TipContent extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content'];

    protected static function boot(): void
    {
        parent::boot();

        self::deleteOld();
    }

    public function tip(): BelongsTo
    {
        return $this->belongsTo(Tip::class);
    }

    public static function deleteOld(): void
    {
        $days = Settings::get('TipsNotifier.deleteOldDays', 30);

        $oldItems = static::query()
            ->where('created_at', '<', now()->subDays($days))
            ->where('favorite', false);

        if ($oldItems->exists()) {
            $deletedCount = $oldItems->delete();

            Log::info("Deleted $deletedCount old tip contents.");
        }
    }
}
