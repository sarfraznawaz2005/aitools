<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use App\Models\NoteFolder;
use App\Traits\InteractsWithToast;
use Cron\CronExpression;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Lorisleiva\CronTranslator\CronTranslator;
use Carbon\Carbon;

class TextNote extends Component
{
    use InteractsWithToast;

    public Note $note;
    public NoteFolder $folder;

    public string $note_folder_id;
    public string $title = '';
    public string $content = '';
    public string $reminder_datetime = '';
    public ?string $recurringFrequency = null;
    public bool $hasReminder = false;
    public bool $isRecurring = false;

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
        $this->resetForm();

        $this->dispatch('showModal', ['id' => 'textNoteModal']);
    }

    #[On('openTextNoteModalEdit')]
    public function openTextNoteModalEdit(Note $note): void
    {
        $this->hasReminder = false;

        $this->note = $note;

        if ($note->reminder_at && $note->reminder_at !== '') {
            $this->hasReminder = true;
        }

        $this->fill($note->toArray());

        $this->dispatch('showModal', ['id' => 'textNoteModal']);
    }

    protected function rules(): array
    {
        $rules = [
            'note_folder_id' => 'required',
            'title' => 'required|min:4',
            'content' => 'required|min:5',
            'reminder_datetime' => 'required_if:hasReminder,true',
            'recurringFrequency' => 'required_if:isRecurring,true',
        ];

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'reminder_datetime.required_if' => 'The reminder date & time field is required when setting a reminder.',
            'recurringFrequency.required_if' => 'The frequency field is required for recurring reminders.',
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
            'is_recurring' => $this->isRecurring,
            'recurring_frequency' => $this->isRecurring ? $this->recurringFrequency : null,
        ])->save();

        $this->success($this->note->wasRecentlyCreated ? 'Note added successfully!' : 'Note saved successfully!');

        $this->dispatch('notesUpdated');
        $this->dispatch('closeModal', ['id' => 'textNoteModal']);

        $this->resetForm();
    }

    #[Computed]
    public function schedulePreview(): string
    {
        if (!$this->hasReminder || !$this->reminder_datetime || !$this->isRecurring || !$this->recurringFrequency) {
            return '';
        }

        try {
            $startDateTime = Carbon::parse($this->reminder_datetime);
            $cronExpression = $this->generateCronExpression($this->recurringFrequency, $startDateTime);
            $humanReadable = CronTranslator::translate($cronExpression);

            return ucfirst($humanReadable);
        } catch (Exception $e) {
            return 'Invalid cron expression';
        }
    }

    #[Computed]
    public function nextRuns(): array
    {
        if (!$this->hasReminder || !$this->reminder_datetime || !$this->isRecurring || !$this->recurringFrequency) {
            return [];
        }

        try {
            $nextRuns = [];
            $startDateTime = Carbon::parse($this->reminder_datetime);
            $cronExpression = new CronExpression($this->generateCronExpression($this->recurringFrequency, $startDateTime));
            $date = $startDateTime;

            for ($i = 0; $i < 3; $i++) {
                $nextRun = $cronExpression->getNextRunDate($date);
                $nextRuns[] = $nextRun->format('Y-m-d H:i:s');
                $date = $nextRun;
            }

            return $nextRuns;
        } catch (Exception $e) {
            return [];
        }
    }

    private function generateCronExpression(string $frequency, Carbon $startDateTime): string
    {
        switch ($frequency) {
            case 'daily':
                return sprintf('%s %s * * *', $startDateTime->minute, $startDateTime->hour);
            case 'weekly':
                return sprintf('%s %s * * %s', $startDateTime->minute, $startDateTime->hour, $startDateTime->dayOfWeek);
            case 'monthly':
                return sprintf('%s %s %s * *', $startDateTime->minute, $startDateTime->hour, $startDateTime->day);
            case 'yearly':
                return sprintf('%s %s %s %s *', $startDateTime->minute, $startDateTime->hour, $startDateTime->day, $startDateTime->month);
            default:
                throw new Exception('Invalid frequency');
        }
    }

    public function resetForm(): void
    {
        $this->reset(['title', 'content', 'reminder_datetime', 'isRecurring', 'recurringFrequency']);

        $this->resetErrorBag();

        $this->note = new Note();
        $this->note_folder_id = $this->folder->id ?? '';
    }
}
