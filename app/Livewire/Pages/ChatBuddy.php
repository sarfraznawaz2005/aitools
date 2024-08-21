<?php

namespace App\Livewire\Pages;

use App\Constants;
use App\Enums\ApiKeyTypeEnum;
use App\Models\Bot;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\DocumentSearchService;
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

            $latestMessage = $conversation
                ->messages()
                ->where('body', '=', Constants::CHATBUDDY_LOADING_STRING)
                ->latest()
                ->first();

            try {

                $prompt = $conversation->bot->prompt;
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

            $files = $conversation->bot->files();

            if (!$files) {
                sendStream("No files found!");
                sendStream("", true);
                return;
            }

            $userQuery = $conversation->messages()->where('is_ai', false)->latest()->first();

            $latestMessage = $conversation
                ->messages()
                ->where('body', '=', Constants::CHATBUDDY_LOADING_STRING)
                ->latest()
                ->first();

            try {

                $markdown = app(MarkdownRenderer::class);
                $llm = getSelectedLLMProvider(Constants::CHATBUDDY_SELECTED_LLM_KEY);
                $batchSize = $this->getEmbeddingsBatchSize();

                // todo: add stand alone llm answer?
                // todo: what chunk size is best?
                $searchService = DocumentSearchService::getInstance($llm, $conversation->id, 5000, $batchSize);

                $isIndexingDone = true;
                foreach ($files as $file) {
                    if (!$searchService->isEmbdeddingDone($file, $conversation->id)) {
                        $isIndexingDone = false;
                    }
                }

                if (!$isIndexingDone) {
                    sendStream("Indexing file data, please wait...");
                }

                $results = $searchService->searchDocuments($files, $userQuery->body);

                $context = '';
                foreach ($results as $result) {
                    $context .= $result['matchedChunk']['text'] . "\nMetadata:" . json_encode($result['matchedChunk']['metadata']) . "\n\n";
                }

                $attachedFiles = implode(',', array_map(fn($file) => basename($file), $files));
                $attachedFilesCount = count(array_map(fn($file) => basename($file), $files));
                $latestMessages = $this->getLatestMessages($conversation);
                $uniqueMessages = $this->getUniqueMessages($latestMessages, $userQuery);
                $conversationHistory = implode("\n", $uniqueMessages);

                $prompt = <<<PROMPT
                    You are an AI assistant designed to answer questions based on provided context and conversation history.
                    Your task is to provide helpful and accurate answers to user queries.

                    First, carefully read and analyze the following context:

                    <context>
                    You have been provided below context from $attachedFilesCount document(s) named $attachedFiles.\n
                    $context
                    </context>

                    Now, consider the conversation history:

                    <conversation_history>
                    $conversationHistory
                    </conversation_history>

                    Here is the user's current query:

                    <query>
                    $userQuery->body
                    </query>

                    Using the provided context and conversation history, formulate a helpful answer to the query.
                    Follow these guidelines:

                    1. Base your answer primarily on the information given in the context.
                    2. If the information needed to answer the query is not present in the context, look for relevant details in the conversation history.
                    3. Always use the conversation history to maintain consistency and provide relevant follow-ups if applicable.
                    4. Ensure your answer is clear, detailed, and directly addresses the query.
                    5. If the answer can be found in the context, provide specific details and explanations.
                    6. If you need to make any assumptions or inferences, clearly state them as such.

                    Please always try to provide Metadata information along with the answer in format:

                    <span class="text-xs">Sources: <source document here, Pages></span>
                    <span class="text-xs">Example: (Document1.pdf, pages: 1, 2, 3)</span>

                    of below format if pages are not mentioned or available:

                    <span class="text-xs">Sources: <source document here></span>
                    <span class="text-xs">Example: (Document1.txt)</span>

                    Do not mention sources if not available.

                    If the information needed to answer the query is not present in the context or conversation history,
                    or if you are unsure about the answer, respond with "Sorry, I don't have enough information to answer
                    this question accurately." and then build few suggested queries for user to ask out of context and
                    converation history. DO NOT ATTEMPT TO MAKE UP OR GUESS AN ANSWER.

                    Your Answer:
                PROMPT;

                Log::info("\n" . str_repeat('-', 100) . "\n" . $prompt . "\n");

                $consolidatedResponse = '';

                $llm->chat($prompt, true, function ($chunk) use (&$consolidatedResponse) {
                    $consolidatedResponse .= $chunk;

                    sendStream($chunk);
                });

                $latestMessage->update(['body' => $markdown->toHtml($consolidatedResponse)]);

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

            if ($message->id === $userQuery->id) {
                continue;
            }

            $formattedMessage = ($message->is_ai ? 'ASSISTANT: ' : 'USER: ') . $message->body;

            if (!in_array($formattedMessage, $uniqueMessages)) {
                $uniqueMessages[] = htmlToText($formattedMessage);
            }
        }
        return $uniqueMessages;
    }

    protected function getEmbeddingsBatchSize(): int
    {
        $llmModel = getSelectedLLMModel(Constants::CHATBUDDY_SELECTED_LLM_KEY);

        return match ($llmModel->llm_type) {
            ApiKeyTypeEnum::GEMINI->value => 100,
            ApiKeyTypeEnum::OPENAI->value => 2000,
            default => 50,
        };
    }
}
