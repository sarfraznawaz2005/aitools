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
    ];

    protected static function booted()
    {
        static::created(function ($note) {
            info('A note was created: ', ['note' => $note->id]);
        });

        static::updated(function ($note) {
            info('A note was updated: ', ['note' => $note->id]);
        });

        static::deleted(function ($note) {
            info('A note was deleted: ', ['note' => $note->id]);
        });
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(NoteFolder::class);
    }

    public function scopeWithNonRecurringReminderAt($query, Carbon $time)
    {
        $formattedTime = $time->format('Y-m-d H:i');
        //info('Checking for non-recurring reminders at: ' . $formattedTime);

        $query = $query->whereRaw("strftime('%Y-%m-%d %H:%M', reminder_at) = ?", [$formattedTime])->where('is_recurring', false);
        //dump($query->toRawSql());

        return $query;
    }


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
                    ->whereRaw("strftime('%H:%M', reminder_at) = ?", ["$hour:$minute"])
                    ->orWhere('recurring_frequency', 'weekly')
                    ->whereRaw("strftime('%w', reminder_at) = ?", [$dayOfWeek])
                    ->whereRaw("strftime('%H:%M', reminder_at) = ?", ["$hour:$minute"])
                    ->orWhere('recurring_frequency', 'monthly')
                    ->whereRaw("strftime('%d', reminder_at) = ?", [$day])
                    ->whereRaw("strftime('%H:%M', reminder_at) = ?", ["$hour:$minute"])
                    ->orWhere('recurring_frequency', 'yearly')
                    ->whereRaw("strftime('%m-%d', reminder_at) = ?", ["$month-$day"])
                    ->whereRaw("strftime('%H:%M', reminder_at) = ?", ["$hour:$minute"]);
            });
    }

}
