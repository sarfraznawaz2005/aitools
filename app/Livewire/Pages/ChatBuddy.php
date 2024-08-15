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

                echo "event: update\n";
                echo "data: " . json_encode("Error, conversation has been deleted!") . "\n\n";
                ob_flush();
                flush();

                echo "event: update\n";
                echo "data: <END_STREAMING_SSE>\n\n";
                ob_flush();
                flush();

            }, 200, [
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]);
        }

        return response()->stream(function () use ($conversation) {

            try {

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
                    $formattedMessage = ($message->is_ai ? 'ASSISTANT: ' : 'USER: ') . $message->body;

                    if (!in_array($formattedMessage, $uniqueMessages)) {
                        $uniqueMessages[] = htmlToText($formattedMessage);
                    }
                }

                $conversationHistory = implode("\n", $uniqueMessages);

                $prompt = <<<PROMPT
                Before answering any question, always refer to the conversation history provided. This will help you understand the
                context of the user's query and provide more relevant and personalized responses. The conversation history will be
                provided in the following format:

                <conversation_history>
                $conversationHistory
                </conversation_history>

                When answering questions, follow these guidelines:
                1. If the conversation history contains relevant information about the question, use it to inform your answer.
                2. If the conversation history does not contain any information about the question, answer from your own knowledge base.
                3. Be clear, detailed, and accurate in your responses.
                4. Offer additional information or suggestions that might be helpful to the user.
                5. If you're unsure about something, admit it and offer to find more information if possible.
                6. Maintain a friendly and supportive tone throughout your response.

                If the user asks the same question again, try to provide a different perspective or additional information in your answer.
                This will help keep the conversation engaging and informative.

                Here is the question you need to answer:

                <question>
                $userQuery->body
                </question>

                Provide your answer within <answer> tags. Please use markdown formatting in your response unless otherwise instructed above.
                PROMPT;

                //Log::info($prompt);

                $markdown = app(MarkdownRenderer::class);

                if (Constants::TEST_MODE) {
                    sleep(1);

                    $text = Constants::TEST_MESSAGE;

                    echo "event: update\n";
                    echo "data: " . json_encode($text) . "\n\n";
                    ob_flush();
                    flush();

                    $latestMessage->update(['body' => $markdown->toHtml($text)]);

                    return;
                }

                $consolidatedResponse = '';
                $llm = getSelectedLLMProvider(Constants::CHATBUDDY_SELECTED_LLM_KEY);

                $llm->chat($prompt, true, function ($chunk) use (&$consolidatedResponse) {
                    $consolidatedResponse .= $chunk;

                    echo "event: update\n";
                    echo "data: " . json_encode($chunk) . "\n\n";
                    ob_flush();
                    flush();
                });

                //Log::info("consolidatedResponse: $consolidatedResponse");
                $latestMessage->update(['body' => $markdown->toHtml($consolidatedResponse)]);

            } catch (Exception $e) {
                Log::error(__CLASS__ . ': ' . $e->getMessage());
                $error = '<span class="text-red-600">Oops! Failed to get a response, please try again.' . ' ' . $e->getMessage() . '</span>';

                echo "event: update\n";
                echo "data: " . json_encode($error) . "\n\n";
                ob_flush();
                flush();

                //$latestMessage->delete();
                $latestMessage->update(['body' => $error]);
            } finally {
                echo "event: update\n";
                echo "data: <END_STREAMING_SSE>\n\n";
                ob_flush();
                flush();
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
