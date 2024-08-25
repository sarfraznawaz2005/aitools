<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Sajadsdi\LaravelSettingPro\Support\Setting;

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
        $days = 30;

        if (Setting::select('TipsNotifier')->has('deleteOldDays')) {
            $days = Setting::select('TipsNotifier')->get('deleteOldDays');
        }

        $oldItems = static::query()
            ->where('created_at', '<', now()->subDays($days))
            ->where('favorite', false);

        if ($oldItems->exists()) {
            $deletedCount = $oldItems->delete();

            Log::info("Deleted $deletedCount old conversations");
        }
    }
}
