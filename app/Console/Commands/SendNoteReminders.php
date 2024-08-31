<?php

namespace App\Console\Commands;

use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Native\Laravel\Facades\System;

class SendNoteReminders extends Command
{
    protected $signature = 'app:send-note-reminders';
    protected $description = 'Send reminders for scheduled notes';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $now = Carbon::now();

        $now->setTimezone(System::timezone() ?? 'Asia/Karachi');

        // Handle non-recurring reminders
        $nonRecurringNotes = Note::withNonRecurringReminderAt($now)->get();
        //info('Non-recurring reminders to be sent: ' . $nonRecurringNotes->count());

        foreach ($nonRecurringNotes as $note) {
            $this->sendReminder($note);
        }

        // Handle recurring reminders
        $recurringNotes = Note::withRecurringReminderAt($now)->get();
        //info('Recurring reminders to be sent: ' . $recurringNotes->count());

        foreach ($recurringNotes as $note) {
            $this->sendReminder($note);
        }

        $this->info('Reminders sent successfully.');
    }

    private function sendReminder(Note $note): void
    {
        info("Reminder sent for note ID {$note->id}: {$note->title}");
    }
}
