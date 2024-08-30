<?php

namespace App\Livewire\Notes;

use App\Enums\BotTypeEnum;
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

class TextNote extends Component
{
    use InteractsWithToast;

    public Note $note;
    public NoteFolder $folder;

    public string $note_folder_id;
    public string $title = '';
    public string $content = '';
    public $reminder_at;
    public bool $hasReminder = false;

    public function mount(): void
    {
        $this->note_folder_id = $this->folder->id ?? '';
    }

    #[Computed]
    public function folders(): Collection
    {
        return NoteFolder::query()->with('notes')->orderBy('name')->get();
    }

    #[On('openCustomModal')]
    public function openCustomModal(): void
    {
        $this->resetForm();

        $this->dispatch('showModal', ['id' => 'addCustomNoteModal']);
    }

    #[On('openCustomModalForEdit')]
    public function openCustomModalForEdit(Note $note): void
    {
        $this->note = $note;

        $this->fill($note->toArray());

        $this->dispatch('showModal', ['id' => 'addCustomNoteModal']);
    }

    protected function rules(): array
    {
        $rules = [
            'note_folder_id' => 'required',
            'title' => 'required|min:4',
            'content' => 'required|min:5',
            'reminder_at' => 'required_if:hasReminder,true',
        ];

        if ($this->reminder_at && $this->hasReminder) {
            $rules['reminder_at'] = 'valid_cron';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'reminder_at.required_if' => 'The frequency field is required.',
            'reminder_at.valid_cron' => 'The frequency format is invalid. Please use a valid cron expression.',
        ];
    }

    public function saveNote(): void
    {
        $this->validate();

        $this->note->fill([
            'note_folder_id' => $this->note_folder_id,
            'title' => $this->title,
            'content' => $this->content,
            'reminder_at' => $this->hasReminder ? $this->reminder_at ?? null : null,
        ])->save();

        $this->success($this->note->wasRecentlyCreated ? 'Note added successfully!' : 'Note saved successfully!');

        $this->dispatch('notesUpdated');
        $this->dispatch('closeModal', ['id' => 'addCustomNoteModal']);

        $this->resetForm();
    }

    #[Computed]
    public function schedulePreview(): string
    {
        try {
            $humanReadable = CronTranslator::translate(trim($this->reminder_at));
            return ucfirst($humanReadable);
        } catch (Exception) {
            return 'Invalid cron expression';
        }
    }

    #[Computed]
    public function nextRuns(): array
    {
        try {
            $nextRuns = [];
            $date = now();
            $cronExpression = new CronExpression(trim($this->reminder_at));

            for ($i = 0; $i < 3; $i++) {
                $nextRun = $cronExpression->getNextRunDate($date);
                $nextRuns[] = $nextRun->format('Y-m-d H:i:s');
                $date = $nextRun;
            }

            return $nextRuns;
        } catch (Exception) {
            return [];
        }
    }

    public function resetForm(): void
    {
        $this->reset(['title', 'content', 'reminder_at']);

        $this->resetErrorBag();

        $this->note = new Note();
        $this->note_folder_id = $this->folder->id ?? '';
        $this->hasReminder = $this->note->reminder_at !== null;
    }
}
