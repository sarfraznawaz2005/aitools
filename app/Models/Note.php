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
        'is_recurring',
        'recurring_frequency',
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
        return $query->where('reminder_at', $time->format('Y-m-d H:i:s'))->where('is_recurring', false);
    }

    /**
     * Scope to get notes that have a recurring reminder at the given time.
     */
    public function scopeWithRecurringReminderAt($query, Carbon $time)
    {
        $formattedDate = $time->format('Y-m-d');
        $hour = $time->format('H');
        $minute = $time->format('i');
        $dayOfWeek = $time->format('N'); // 1 (for Monday) through 7 (for Sunday)
        $day = $time->format('d');
        $month = $time->format('m');

        return $query->where('is_recurring', true)
            ->where(function ($query) use ($formattedDate, $hour, $minute, $dayOfWeek, $day, $month) {
                $query->where('recurring_frequency', 'hourly')
                    ->whereRaw("strftime('%Y-%m-%d', reminder_at) = ?", [$formattedDate])
                    ->whereRaw("strftime('%H', reminder_at) <= ?", [$hour])
                    ->whereRaw("strftime('%M', reminder_at) = ?", [$minute])
                    ->orWhere('recurring_frequency', 'daily')
                    ->whereRaw("strftime('%H', reminder_at) = ?", [$hour])
                    ->whereRaw("strftime('%M', reminder_at) = ?", [$minute])
                    ->orWhere('recurring_frequency', 'weekly')
                    ->whereRaw("strftime('%w', reminder_at) = ?", [$dayOfWeek])
                    ->whereRaw("strftime('%H', reminder_at) = ?", [$hour])
                    ->whereRaw("strftime('%M', reminder_at) = ?", [$minute])
                    ->orWhere('recurring_frequency', 'monthly')
                    ->whereRaw("strftime('%d', reminder_at) = ?", [$day])
                    ->whereRaw("strftime('%H', reminder_at) = ?", [$hour])
                    ->whereRaw("strftime('%M', reminder_at) = ?", [$minute])
                    ->orWhere('recurring_frequency', 'yearly')
                    ->whereRaw("strftime('%m', reminder_at) = ?", [$month])
                    ->whereRaw("strftime('%d', reminder_at) = ?", [$day])
                    ->whereRaw("strftime('%H', reminder_at) = ?", [$hour])
                    ->whereRaw("strftime('%M', reminder_at) = ?", [$minute]);
            });
    }

}
