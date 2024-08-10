<?php

namespace App\Actions;

use App\Models\Conversation;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatBuddyChatAction
{
    public function __invoke(Conversation $conversation): StreamedResponse
    {
        return response()->stream(function () use ($conversation) {
            $userQuery = $conversation->messages()->where('is_ai', false)->orderByDesc('id')->first();
            $latestMessages = $conversation->messages()->latest()->limit(5)->get()->sortBy('id');
            $conversationHistory = '';

            foreach ($latestMessages as $message) {
                $conversationHistory .= $message->is_ai ? 'YOU:' : 'USER:' . $message->body . "\n";
            }

            $prompt = "You are a helpful and enthusiastic support bot who can answer a given question.
            Before answering, always refer to the conversation history to know what user is asking or
            talking about. If provided conversation history does not contain any information about the
            question then answer from your own knowledge.

            conversation history:$conversationHistory
            question: $userQuery
            answer: ";

            Log::info($prompt);

            $llm = getChatBuddyLLMProvider();

            $consolidatedResponse = '';

            $llm->chat($prompt, true, function($chunk) use (&$consolidatedResponse) {
                $consolidatedResponse .= $chunk;

                echo "event: update\n";
                echo "data: " . json_encode($chunk) . "\n\n";
                ob_flush();
                flush();
            });

            Log::info("consolidatedResponse: $consolidatedResponse");

            // Save the consolidated response to the database
//            $conversation->messages()->create([
//                'body' => $consolidatedResponse,
//                'is_ai' => true,
//            ]);

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
}
