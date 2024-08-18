<?php

namespace App\Livewire\Pages;

use App\Constants;
use App\Models\Conversation;
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

                sendStream("Error, conversation has been deleted!", true);

                sendStream("", true);

            }, 200, [
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]);
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

                if (file_exists('originalBotPrompt.txt')) {
                    $conversationHistory .= "\nMore Context:" . file_get_contents('originalBotPrompt.txt');
                    @unlink('originalBotPrompt.txt');
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
}
