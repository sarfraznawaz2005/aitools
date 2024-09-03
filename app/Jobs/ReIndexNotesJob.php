<?php

namespace App\Jobs;

use App\Constants;
use App\Models\Note;
use App\Services\NotesSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReIndexNotesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // The number of seconds the job can run before timing out.
    public bool $failOnTimeout = true; // If the job should fail when it times out.
    public bool $deleteWhenMissingModels = true; // If the job should be deleted if the model no longer exists.

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        info('Re-indexing Notes...');

        $llm = getSelectedLLMProvider(Constants::NOTES_SELECTED_LLM_KEY);

        // get all notes
        $notes = Note::with('folder')->get()->map(function ($note) {
            return [
                'id' => $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'folder' => $note->folder->name,
            ];
        })->toArray();

        # important: delete the old index file
        @unlink(storage_path('app/notes.json'));

        $searchService = NotesSearchService::getInstance($llm);
        $searchService->searchTexts($notes, 'whatever');

        info('Indexing Done...');
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('ReIndexNotesJob failed: ' . $exception->getMessage());
    }
}
