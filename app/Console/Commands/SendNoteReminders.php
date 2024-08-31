<?php

namespace App\Console\Commands;

use App\Events\NoteSucessEvent;
use App\Models\Note;
use Carbon\Carbon;
use Exception;
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
        try {

            $now = Carbon::now();

            # important
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

        } catch (Exception) {
            // failed due to some error
        }
    }

    private function sendReminder(Note $note): void
    {
        //info("Reminder sent for note ID {$note->id}: {$note->title}");

        NoteSucessEvent::broadcast($note);
    }
}
