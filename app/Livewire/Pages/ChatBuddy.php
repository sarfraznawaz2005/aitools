<?php

namespace App\Livewire\Pages;

use App\Constants;
use App\Models\Bot;
use App\Models\Conversation;
use App\Services\DocumentSearchService;
use App\Traits\InteractsWithToast;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
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

            try {

                $prompt = $conversation->bot->prompt;
                $userQuery = $conversation->messages()->where('is_ai', false)->latest()->first();

                $latestMessage = $conversation
                    ->messages()
                    ->where('body', '=', Constants::CHATBUDDY_LOADING_STRING)
                    ->latest()
                    ->first();

                $latestMessages = $conversation
                    ->messages()
                    ->where('body', '!=', Constants::CHATBUDDY_LOADING_STRING)
                    ->whereNot(function ($query) {
                        $query
                            ->where('body', 'like', '%conversation history%')
                            ->orWhere('body', 'like', '%provided context%');
                    })
                    ->latest()
                    ->limit(Constants::CHATBUDDY_TOTAL_CONVERSATION_HISTORY)
                    ->get()
                    ->sortBy('id');

                $uniqueMessages = [];
                foreach ($latestMessages as $message) {

                    if ($message->id === $userQuery->id) {
                        continue;
                    }

                    $formattedMessage = ($message->is_ai ? 'ASSISTANT: ' : 'USER: ') . $message->body;

                    if (!in_array($formattedMessage, $uniqueMessages)) {
                        $uniqueMessages[] = htmlToText($formattedMessage);
                    }
                }

                $conversationHistory = implode("\n", $uniqueMessages);

                // switch to general bot temporarily in case of forced mode
                if (file_exists('forceAnswer')) {
                    $generalBotPrompt = Bot::query()->where('name', 'General')->first()->prompt;
                    $prompt = $generalBotPrompt;
                    @unlink('forceAnswer');
                }

                // add user's current question
                $conversationHistory .= "\nUSER:" . $userQuery->body;

                $prompt = makePromopt($userQuery->body, $conversationHistory, $prompt, 2);

                Log::info("\n" . str_repeat('-', 100) . "\n" . $prompt . "\n");

                $markdown = app(MarkdownRenderer::class);

                if (Constants::TEST_MODE) {
                    sleep(1);

                    $text = Constants::TEST_MESSAGE;

                    sendStream($text);

                    $latestMessage->update(['body' => $markdown->toHtml($text)]);

                    return;
                }

                $consolidatedResponse = '';
                $llm = getSelectedLLMProvider(Constants::CHATBUDDY_SELECTED_LLM_KEY);

                $llm->chat($prompt, true, function ($chunk) use (&$consolidatedResponse) {
                    $consolidatedResponse .= $chunk;

                    sendStream($chunk);
                });

                //Log::info("consolidatedResponse: $consolidatedResponse");
                $latestMessage->update(['body' => $markdown->toHtml($consolidatedResponse)]);

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

            $prompt = $conversation->bot->prompt;
            $userQuery = $conversation->messages()->where('is_ai', false)->latest()->first();

            $latestMessage = $conversation
                ->messages()
                ->where('body', '=', Constants::CHATBUDDY_LOADING_STRING)
                ->latest()
                ->first();

            $files = $conversation->bot->files();

            if (!$files) {
                sendStream("No files found!");
                sendStream("", true);
                return;
            }

            try {

                $llm = getSelectedLLMProvider(Constants::CHATBUDDY_SELECTED_LLM_KEY);

                $searchService = new DocumentSearchService($llm, $conversation->id, 1000, 0.6, 3);
                $result = $searchService->searchDocuments($files, $userQuery->body);

                if (!$result) {
                    $message = "Sorry, no results found for given query!";
                    sendStream($message);

                    $latestMessage->update(['body' => $message]);

                    return;
                }

                $answer = $result['text'] . '<hr style="margin:10px 0;">Source: <strong>' . $result['source'] . '</strong>';

                sendStream($answer);

                $latestMessage->update(['body' => $answer]);

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

    function splitTextIntoChunks($text, $chunkSize = 1000): array
    {
        $chunks = [];
        $currentChunk = "";
        $currentLength = 0;

        for ($i = 0; $i < strlen($text); $i++) {
            $currentChunk .= $text[$i];
            $currentLength++;

            if ($currentLength >= $chunkSize) {
                $chunks[] = trim($currentChunk);
                $currentChunk = "";
                $currentLength = 0;
            }
        }

        // Add the last chunk
        if (!empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    protected function cosineSimilarity($u, $v): float|int
    {
        $dotProduct = 0;
        $uLength = 0;
        $vLength = 0;

        for ($i = 0; $i < count($u); $i++) {
            $dotProduct += $u[$i] * $v[$i];
            $uLength += $u[$i] * $u[$i];
            $vLength += $v[$i] * $v[$i];
        }

        $uLength = sqrt($uLength);
        $vLength = sqrt($vLength);

        return $dotProduct / ($uLength * $vLength);
    }

    function getCleanedText(string $text): string|array|null
    {
        $cleanedText = strip_tags($text);
        $cleanedText = preg_replace('/\s+/', ' ', $cleanedText);
        $cleanedText = preg_replace('/\r\n|\r/', "\n", $cleanedText);
        $cleanedText = preg_replace('/(\s*\n\s*){3,}/', "\n\n", $cleanedText);
        $cleanedText = str_replace(["\r\n", "\r", "\n"], ' ', $cleanedText);

        return trim($cleanedText);
    }
}
