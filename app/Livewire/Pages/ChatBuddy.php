<?php

namespace App\Livewire\Pages;

use App\Constants;
use App\Models\Bot;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\JsonFileVectorStore;
use App\Traits\InteractsWithToast;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatBuddy extends Component
{
    use InteractsWithToast;

    public Conversation $conversation;

    public function chat(Conversation $conversation = null): StreamedResponse
    {
        if (is_null($conversation)) {
            return response()->stream(function () {

                sendStream("Error, conversation has been deleted!");
                sendStream("", true);

            }, 200, [
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]);
        }

        if ($conversation->bot->isDocumentBot()) {
            return $this->chatWithDocs($conversation);
        }

        return response()->stream(function () use ($conversation) {

            $this->dispatch('conversationsUpdated');

            $latestMessage = $conversation
                ->messages()
                ->where('body', '=', Constants::CHATBUDDY_LOADING_STRING)
                ->latest()
                ->first();

            try {

                $userQuery = $conversation->messages()->where('is_ai', false)->latest()->first();

                $markdown = app(MarkdownRenderer::class);

                if (Constants::TEST_MODE) {
                    sleep(1);

                    $text = Constants::TEST_MESSAGE;

                    sendStream($text);

                    $latestMessage->update(['body' => $markdown->toHtml($text)]);

                    return;
                }

                $latestMessages = $this->getLatestMessages($conversation);
                $uniqueMessages = $this->getUniqueMessages($latestMessages, $userQuery);
                $conversationHistory = implode("\n", array_map(fn($message) => htmlToText($message), $uniqueMessages));

                // add user's current question
                $conversationHistory .= "\nUSER:" . $userQuery->body;

                $prompt = makePromptForTextBot($conversation->bot, $userQuery->body, $conversationHistory, 2);

                // switch to general bot temporarily in case of forced mode
                if (file_exists('forceAnswer')) {
                    $generalBotPrompt = Bot::query()->where('name', 'General')->first()->prompt;
                    $prompt = $generalBotPrompt;
                    @unlink('forceAnswer');
                }

                $consolidatedResponse = '';
                $llm = getSelectedLLMProvider(Constants::CHATBUDDY_SELECTED_LLM_KEY);

                $llm->chat($prompt, true, function ($chunk) use (&$consolidatedResponse) {
                    $consolidatedResponse .= $chunk;

                    sendStream($chunk);
                });

                $selectedModel = getSelectedLLMModel(Constants::CHATBUDDY_SELECTED_LLM_KEY);

                //Log::info("consolidatedResponse: $consolidatedResponse");

                $latestMessage->update([
                    'body' => processMarkdownToHtml($consolidatedResponse),
                    'llm' => $selectedModel->llm_type . ' (' . $selectedModel->model_name . ')' ?? '',
                ]);

            } catch (Exception $e) {
                Log::error(__CLASS__ . ': ' . $e->getMessage());
                $error = '<span class="text-red-600">Oops! Failed to get a response, please try again.' . ' ' . $e->getMessage() . '</span>';

                sendStream($error);

                //$latestMessage->delete();
                $latestMessage->update(['body' => $error]);
            } finally {
                sendStream("", true);
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    #[Title('Chat Buddy')]
    public function render(): Application|View|Factory
    {
        if (session()->has('addBotMessage')) {
            $this->dispatch('getChatBuddyAiResponse', session('addBotMessage'));
        }

        if (session()->has('conversationsDeleted')) {
            $this->success('Conversations deleted successfully.');
        }

        return view('livewire.pages.chat-buddy');
    }

    protected function chatWithDocs(Conversation $conversation): StreamedResponse
    {
        return response()->stream(function () use ($conversation) {

            $this->dispatch('conversationsUpdated');

            $latestMessage = $conversation
                ->messages()
                ->where('body', '=', Constants::CHATBUDDY_LOADING_STRING)
                ->latest()
                ->first();

            $files = $conversation->bot->files();

            if (!$files) {
                sendStream("No files found!");
                sendStream("", true);
                $latestMessage->update(['body' => "No files found!"]);
                return;
            }

            $userQuery = $conversation->messages()->where('is_ai', false)->latest()->first();

            try {

                $llm = getSelectedLLMProvider(Constants::CHATBUDDY_SELECTED_LLM_KEY);

                $texts = [];
                foreach ($files as $file) {
                    $texts[] = extractTextFromFile($file);
                }

                $flattenedTexts = [];
                foreach ($texts as $subArray) {
                    $flattenedTexts = array_merge($flattenedTexts, $subArray);
                }

                $dataFilePath = storage_path('app/' . 'bot-' . $conversation->bot->id . '-data.json');

                if (!file_exists($dataFilePath)) {
                    sendStream("Indexing file data, please wait...");
                }

                // About Chunk Size: if too long, it will not answer granular details, and if it is too short, it will
                // not answer long details so this is trade off.
                $searchService = JsonFileVectorStore::getInstance($llm, 'bot-' . $conversation->bot->id . '-data.json', 2000);
                $results = $searchService->searchTexts($flattenedTexts, $userQuery->body);

                if (!count($results)) {
                    sendStream(Constants::NO_RESULTS_FOUND, true);
                    $latestMessage->update(['body' => Constants::NO_RESULTS_FOUND]);
                    return;
                }

                $context = '';
                foreach ($results as $result) {
                    $text = $result['matchedChunk']['text'];
                    $context .= $text . "\n\n<sources>" . $result['matchedChunk']['metadata'] . "</sources>\n\n";
                }

                $attachedFiles = implode(',', array_map(fn($file) => basename($file), $files));
                $attachedFilesCount = count(array_map(fn($file) => basename($file), $files));
                $latestMessages = $this->getLatestMessages($conversation);
                $uniqueMessages = $this->getUniqueMessages($latestMessages, $userQuery);
                $conversationHistory = implode("\n", array_map(fn($message) => htmlToText($message), $uniqueMessages));

                $info = "You have been provided below context and contents/details from $attachedFilesCount files/documents named $attachedFiles.\n";
                $prompt = makePromoptForDocumentBot($conversation->bot, $info, $context, $userQuery->body, $conversationHistory);

                $consolidatedResponse = '';

                $llm->chat($prompt, true, function ($chunk) use (&$consolidatedResponse) {
                    $consolidatedResponse .= $chunk;

                    sendStream($chunk);
                });

                $selectedModel = getSelectedLLMModel(Constants::CHATBUDDY_SELECTED_LLM_KEY);

                //info("consolidatedResponse: " . processMarkdownToHtml($consolidatedResponse));

                $latestMessage->update([
                    'body' => processMarkdownToHtml($consolidatedResponse),
                    'llm' => $selectedModel->llm_type . ' (' . $selectedModel->model_name . ')' ?? '',
                ]);

            } catch (Exception $e) {
                Log::error($e->getFile() . ' - Query: "' . $userQuery->body . '" - Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
                $error = '<span class="text-red-600">Oops! Failed to get a response due to some error, please try again.' . ' ' . $e->getMessage() . '</span>';

                sendStream($error);

                //$latestMessage->delete();
                $latestMessage->update(['body' => $error]);
            } finally {
                sendStream("", true);
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    function getLatestMessages(Conversation $conversation): Collection|\Illuminate\Database\Eloquent\Collection
    {
        return $conversation
            ->messages()
            ->where('body', '!=', Constants::CHATBUDDY_LOADING_STRING)
            ->whereNot(function ($query) {
                $query
                    ->where('body', 'like', '%conversation history%')
                    ->orWhere('body', 'like', '%have enough information to answer this question accurately%')
                    ->orWhere('body', 'like', '%provided context%');
            })
            ->latest()
            ->limit(Constants::CHATBUDDY_TOTAL_CONVERSATION_HISTORY)
            ->get()
            ->sortBy('id');
    }

    function getUniqueMessages($latestMessages, Message $userQuery): array
    {
        $uniqueMessages = [];
        foreach ($latestMessages as $message) {

            // do not add latest user query to conversation history
            if ($message->id === $userQuery->id) {
                continue;
            }

            $formattedMessage = ($message->is_ai ? 'ASSISTANT: ' : 'USER: ') . $message->body;

            if (!$message->is_ai) {
                $uniqueMessages[] = $formattedMessage; // allow all user messages
            } else {
                if (!in_array($formattedMessage, $uniqueMessages)) {
                    $uniqueMessages[] = htmlToText($formattedMessage);
                }
            }

        }

        return $uniqueMessages;
    }
}
