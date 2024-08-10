<?php

namespace App\Actions;

use App\Models\Conversation;

class ChatBuddyChatAction
{
    public function __invoke(Conversation $conversation)
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

            $output = '';

            $llm = getChatBuddyLLMProvider();

            $text = $llm->chat($prompt, true);

            $output .= $text;

            echo "event: update\n";
            echo 'data: ' . $text;
            echo "\n\n";

            ob_flush();

            // Rewrite the last message with the full output
            $latestMessages->last()->update(['body' => $output]);

            echo "event: update\n";
            echo 'data: <END_STREAMING_SSE>';
            echo "\n\n";

            ob_flush();
            flush();

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }
}
