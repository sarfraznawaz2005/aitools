<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_folder_id',
        'title',
        'content',
        'reminder_at',
        'html',
        'width',
        'height',
        'ratio',
        'url',
        'image',
        'author',
        'author_url',
        'source',
        'source_url',
        'source_icon',
        'published_at',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(NoteFolder::class);
    }

    /**
     * Scope to get notes that have a non-recurring reminder at the given time.
     */
    public function scopeWithNonRecurringReminderAt($query, Carbon $time)
    {
        return $query->where('reminder_at', $time->format('Y-m-d H:i:s'))
            ->where('is_recurring', false);
    }

    /**
     * Scope to get notes that have a recurring reminder at the given time.
     */
    public function scopeWithRecurringReminderAt($query, Carbon $time)
    {
        return $query->where('is_recurring', true)
            ->where(function ($query) use ($time) {
                $query->where('recurring_frequency', 'hourly')
                    ->whereRaw("DATE(reminder_at) = ?", [$time->format('Y-m-d')])
                    ->whereRaw("HOUR(reminder_at) <= ?", [$time->hour])
                    ->whereRaw("MINUTE(reminder_at) = ?", [$time->minute])
                    ->orWhere('recurring_frequency', 'daily')
                    ->whereRaw("HOUR(reminder_at) = ?", [$time->hour])
                    ->whereRaw("MINUTE(reminder_at) = ?", [$time->minute])
                    ->orWhere('recurring_frequency', 'weekly')
                    ->whereRaw("DAYOFWEEK(reminder_at) = DAYOFWEEK(?)", [$time])
                    ->whereRaw("HOUR(reminder_at) = ?", [$time->hour])
                    ->whereRaw("MINUTE(reminder_at) = ?", [$time->minute])
                    ->orWhere('recurring_frequency', 'monthly')
                    ->whereRaw("DAY(reminder_at) = ?", [$time->day])
                    ->whereRaw("HOUR(reminder_at) = ?", [$time->hour])
                    ->whereRaw("MINUTE(reminder_at) = ?", [$time->minute])
                    ->orWhere('recurring_frequency', 'yearly')
                    ->whereRaw("MONTH(reminder_at) = ?", [$time->month])
                    ->whereRaw("DAY(reminder_at) = ?", [$time->day])
                    ->whereRaw("HOUR(reminder_at) = ?", [$time->hour])
                    ->whereRaw("MINUTE(reminder_at) = ?", [$time->minute]);
            });
    }
}
