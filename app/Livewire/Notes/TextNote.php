<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use App\Models\NoteFolder;
use App\Traits\InteractsWithToast;
use Carbon\Carbon;
use Cron\CronExpression;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Lorisleiva\CronTranslator\CronTranslator;

class TextNote extends Component
{
    use InteractsWithToast;

    public Note $note;
    public NoteFolder $folder;

    public string $note_folder_id;
    public string $title = '';
    public string $content = '';
    public string $reminder_datetime = '';
    public ?string $recurring_frequency = null;
    public bool $hasReminder = false;
    public bool $is_recurring = false;

    public function mount(): void
    {
        $this->note_folder_id = $this->folder->id ?? '';
    }

    #[Computed]
    public function folders(): Collection
    {
        return NoteFolder::query()->with('notes')->orderBy('name')->get();
    }

    #[On('openTextNoteModal')]
    public function openTextNoteModal(): void
    {
        $this->hasReminder = false;

        $this->resetForm();

        $this->dispatch('showModal', ['id' => 'textNoteModal']);
    }

    #[On('openTextNoteModalEdit')]
    public function openTextNoteModalEdit(Note $note): void
    {
        $this->note = $note;

        $this->hasReminder = false;
        if ($note->reminder_at && $note->reminder_at !== '' && $note->reminder_at !== null) {
            $this->hasReminder = true;
            $this->reminder_datetime = $note->reminder_at;
        }

        $this->fill($note->toArray());

        $this->dispatch('showModal', ['id' => 'textNoteModal']);
    }

    protected function rules(): array
    {
        return [
            'note_folder_id' => 'required',
            'title' => 'required|min:4',
            'content' => 'required|min:5',
            'reminder_datetime' => 'required_if:hasReminder,true',
            'recurring_frequency' => 'required_if:is_recurring,true',
        ];
    }

    protected function messages(): array
    {
        return [
            'reminder_datetime.required_if' => 'The reminder date & time field is required when setting a reminder.',
            'recurring_frequency.required_if' => 'The frequency field is required for recurring reminders.',
        ];
    }

    public function saveNote(): void
    {
        $this->validate();

        $this->note->fill([
            'note_folder_id' => $this->note_folder_id,
            'title' => $this->title,
            'content' => $this->content,
            'reminder_at' => $this->hasReminder ? $this->reminder_datetime ?? null : null,
            'is_recurring' => $this->is_recurring,
            'recurring_frequency' => $this->is_recurring ? $this->recurring_frequency : null,
        ])->save();

        $this->success($this->note->wasRecentlyCreated ? 'Note added successfully!' : 'Note saved successfully!');

        $this->dispatch('notesUpdated');
        $this->dispatch('closeModal', ['id' => 'textNoteModal']);

        $this->resetForm();
    }

    #[Computed]
    public function schedulePreview(): string
    {
        if (!$this->hasReminder || !$this->reminder_datetime || !$this->is_recurring || !$this->recurring_frequency) {
            return '';
        }

        try {
            $startDateTime = Carbon::parse($this->reminder_datetime);
            $cronExpression = $this->generateCronExpression($this->recurring_frequency, $startDateTime);

            if ($this->recurring_frequency === 'hourly') {
                $humanReadable = "Every hour on " . $startDateTime->format('d M Y');
            } else {
                $humanReadable = CronTranslator::translate($cronExpression);
            }

            return ucfirst($humanReadable);
        } catch (Exception $e) {
            return 'Invalid cron expression';
        }
    }

    #[Computed]
    public function nextRuns(): array
    {
        if (!$this->hasReminder || !$this->reminder_datetime || !$this->is_recurring || !$this->recurring_frequency) {
            return [];
        }

        try {
            $nextRuns = [];
            $startDateTime = Carbon::parse($this->reminder_datetime);

            if ($this->recurring_frequency === 'hourly') {
                // Manually generate the next hourly runs for the specified date
                $date = $startDateTime;
                for ($i = 0; $i < 3; $i++) {
                    $nextRun = $date->addHour();
                    if ($nextRun->isSameDay($startDateTime)) {
                        $nextRuns[] = $nextRun->format('Y-m-d H:i:s');
                    }
                }
            } else {
                // Use CronExpression for non-hourly frequencies
                $cronExpression = new CronExpression($this->generateCronExpression($this->recurring_frequency, $startDateTime));
                $date = $startDateTime;

                for ($i = 0; $i < 3; $i++) {
                    $nextRun = $cronExpression->getNextRunDate($date);
                    $nextRuns[] = $nextRun->format('Y-m-d H:i:s');
                    $date = $nextRun;
                }
            }

            return $nextRuns;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @throws Exception
     */
    private function generateCronExpression(string $frequency, Carbon $startDateTime): string
    {
        return match ($frequency) {
            'hourly' => sprintf('%s * %s %s %s *', $startDateTime->minute, $startDateTime->hour, $startDateTime->day, $startDateTime->month),
            'daily' => sprintf('%s %s * * *', $startDateTime->minute, $startDateTime->hour),
            'weekly' => sprintf('%s %s * * %s', $startDateTime->minute, $startDateTime->hour, $startDateTime->dayOfWeek),
            'monthly' => sprintf('%s %s %s * *', $startDateTime->minute, $startDateTime->hour, $startDateTime->day),
            'yearly' => sprintf('%s %s %s %s *', $startDateTime->minute, $startDateTime->hour, $startDateTime->day, $startDateTime->month),
            default => throw new Exception('Invalid frequency'),
        };
    }


    public function resetForm(): void
    {
        $this->reset(['title', 'content', 'reminder_datetime', 'is_recurring', 'recurring_frequency']);

        $this->resetErrorBag();

        $this->note = new Note();
        $this->note_folder_id = $this->folder->id ?? '';
    }
}
