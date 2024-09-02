<?php

namespace App\Livewire\Notes;

use App\Constants;
use App\Models\Note;
use App\Models\NoteFolder;
use App\Traits\InteractsWithToast;
use Carbon\Carbon;
use Cron\CronExpression;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Lorisleiva\CronTranslator\CronTranslator;
use Pforret\PfArticleExtractor\ArticleExtractor;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class TextNote extends Component
{
    use InteractsWithToast;

    public Note $note;
    public NoteFolder $folder;

    public string $note_folder_id;
    public string $title = '';
    public string $content = '';
    public ?string $recurring_frequency = null;
    public bool $is_recurring = false;

    public string $reminder_datetime = '';
    public bool $hasReminder = false;

    public array $linkErrors = [];
    public bool $useAI = true;

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
        $this->resetForm();

        $this->note = $note;

        $this->hasReminder = false;
        if ($note->reminder_at && $note->reminder_at !== '' && $note->reminder_at !== null) {
            $this->hasReminder = true;
            $this->reminder_datetime = $note->reminder_at;
        }

        $this->fill($note->toArray());

        $this->dispatch('showModal', ['id' => 'textNoteModal']);
    }

    #[On('fetchLink')]
    public function fetchLink(string $link): void
    {
        $this->title = '';
        $this->content = '';

        $validator = Validator::make(['link' => $link], [
            'link' => 'required|url',
        ]);

        if ($validator->fails()) {
            $this->linkErrors = $validator->errors()->toArray();
            return;
        }

        try {

            $html = fetchUrlContent($link);
            //info($html);

            if (!$html) {
                $this->linkErrors = ['link' => 'Failed to fetch content from the provided link, please try again.'];
                return;
            }

            $articleData = ArticleExtractor::getArticle($html);

            $this->title = $articleData->title ?? '';
            $this->content = $articleData->content ?? '';

            if ($this->useAI && strlen($html) > 100) {
                $content = $this->getContentAI($html, $link);

                if ($content === 'No Content Found') {
                    $this->linkErrors = ['link' => 'Failed to extract content from the provided link, please try again.'];
                    return;
                }

                $markdown = app(MarkdownRenderer::class);
                $this->content = $markdown->toHtml($content);

                $this->reset(['useAI']);
            }

            $this->linkErrors = [];
            unset($html);
            unset($articleData);

            $this->dispatch('close-dialog', id: 'linkdialog');
        } catch (Exception) {
            $this->linkErrors = ['link' => 'Failed to fetch content from the provided link, please try again.'];
        }
    }

    private function getContentAI(string $html, string $url): string
    {
        $llm = getSelectedLLMProvider(Constants::NOTES_SELECTED_LLM_KEY);

        $prompt = "
        You are amazing Researcher on the web content.

        Analyze below content and try your best to extract main content or article from html given below. Ensure you
        extract entire article without skipping anything. Follow these rules:

        - Do not tell anything about url, page, author or website itself, only the main content or article.
        - You can use any markdown formatting like bold, italic, code block, etc.
        - Extracted content should have line breaks where necessary for improved readability.
        - Do not skip minor details such as bullet points, images, pre, code, charts from main content, we need these as well.
        - If there is an image (img tag) in main content, convert it into full url based on '$url' so we don't get broken images.
        - If there is table in main content, convert it to markdown table.
        - You can skip styles and javascript code.
        - Convert html tags into markdown counterparts where possible.
        - Your answer must not contain any html tags (except, ul, li, pre, code, img, svg or videos) but you must give your
        answer in markdown foramtted text.

        Finally, if you cannot extract an article or main content from given html, just say 'No Content Found'. Do not
        assume or provide answer from your own knowledge.

        HTML: '$html'
        ";

        return $llm->chat($prompt);
    }

    public function saveNote(): void
    {
        $this->validate([
            'note_folder_id' => 'required',
            'title' => 'required|min:4',
            'recurring_frequency' => 'required_if:is_recurring,true',
            'content' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (str_contains(strtolower($value), '<img') || str_contains(strtolower($value), '<iframe')) {
                        return;
                    }

                    if (!trim(strip_tags($value))) {
                        $fail('The content field is required.');
                    }
                },
            ],
            'reminder_datetime' => [
                'required_if:hasReminder,true',
                function ($attribute, $value, $fail) {
                    if ($this->hasReminder && Carbon::parse($value)->isPast()) {
                        $fail('The reminder date & time cannot be in the past.');
                    }
                },
            ]
        ], [
            'reminder_datetime.required_if' => 'The reminder date & time field is required when setting a reminder.',
            'recurring_frequency.required_if' => 'The frequency field is required for recurring reminders.',
        ]);

        $reminderAt = $this->hasReminder ? Carbon::parse($this->reminder_datetime)->format('Y-m-d H:i:s') : null;

        $this->note->fill([
            'note_folder_id' => $this->note_folder_id,
            'title' => $this->title,
            'content' => $this->content,
            'reminder_at' => $reminderAt,
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
        $this->resetValidation();

        $this->note = new Note();
        $this->note_folder_id = $this->folder->id ?? '';
    }
}
