<?php

namespace App\Console\Commands;

use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Console\Command;

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

        // Handle non-recurring reminders
        $nonRecurringNotes = Note::withNonRecurringReminderAt($now)->get();
        foreach ($nonRecurringNotes as $note) {
            $this->sendReminder($note);
        }

        // Handle recurring reminders
        $recurringNotes = Note::withRecurringReminderAt($now)->get();
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
