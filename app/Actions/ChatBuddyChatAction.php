<?php

namespace App\Actions;

use App\Constants;
use App\Models\Conversation;
use Exception;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatBuddyChatAction
{
    public function __invoke(Conversation $conversation = null): StreamedResponse
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

        $markdown = app(MarkdownRenderer::class);
        $llm = getChatBuddyLLMProvider();

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
                $query->where('body', 'like', '%conversation history%')
                    ->orWhere('body', 'like', '%sorry%')
                    ->orWhere('body', 'like', '%context%');
            })
            ->latest()
            ->limit(Constants::TOTAL_CONVERSATION_HISTORY)
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

        $prompt = "You are a helpful and enthusiastic support assistant who can answer a given question.
                Before answering, always refer to the conversation history to know what user is asking or
                talking about. If provided conversation history does not contain any information about the
                question then answer from your own knowledge Use markdown for your answer. If the user asks
                same question again, try to give different answer each time.

                Conversation History:\n$conversationHistory\n\n
                Question: $userQuery->body
                Your Answer: ";

        //Log::info($prompt);

        return response()->stream(function () use ($markdown, $llm, $latestMessages, $latestMessage, $userQuery, $prompt) {

            try {

                if (Constants::TEST_MODE) {
                    sleep(1);

                    $text = "## Test Message\n\nThis is a **test** message with some *italic* text and a [link](https://google.com).";

                    echo "event: update\n";
                    echo "data: " . json_encode($text) . "\n\n";
                    ob_flush();
                    flush();

                    $latestMessage->update(['body' => $markdown->toHtml($text)]);

                    return;
                }

                $consolidatedResponse = '';

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

                echo "event: update\n";
                echo "data: " . json_encode(Constants::CHATBUDDY_AI_ERROR_MESSSAGE) . "\n\n";
                ob_flush();
                flush();

                //$latestMessage->delete();
                $latestMessage->update(['body' => Constants::CHATBUDDY_AI_ERROR_MESSSAGE]);
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
}
