<?php

namespace App\Actions;

use App\Constants;
use App\Models\Conversation;
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatBuddyChatAction
{
    public function __invoke(Conversation $conversation): StreamedResponse
    {
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
                $uniqueMessages[] = $formattedMessage;
            }
        }

        $conversationHistory = implode("\n", $uniqueMessages);

        $prompt = "You are a helpful and enthusiastic support assistant who can answer a given question.
                Before answering, always refer to the conversation history to know what user is asking or
                talking about. If provided conversation history does not contain any information about the
                question then answer from your own knowledge Use markdown for your answer.

                Conversation History:\n$conversationHistory\n\n
                Question: $userQuery->body
                Your Answer: ";

        //Log::info($prompt);

        return response()->stream(function () use ($llm, $latestMessages, $latestMessage, $userQuery, $prompt) {

            try {

                $consolidatedResponse = '';

                $llm->chat($prompt, true, function ($chunk) use (&$consolidatedResponse) {
                    $consolidatedResponse .= $chunk;

                    echo "event: update\n";
                    echo "data: " . json_encode($chunk) . "\n\n";
                    ob_flush();
                    flush();
                });

                //Log::info("consolidatedResponse: $consolidatedResponse");
                $latestMessage->update(['body' => $consolidatedResponse]);

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