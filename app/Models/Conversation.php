<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenAI\Laravel\Facades\OpenAI;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function addInput(string $message, bool $isAi = false): Message
    {
        return $this->messages()->create([
            'body' => $message,
            'conversation_id' => $this->id,
            'is_ai' => $isAi,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Save the output message of the bot
     */
    public function addOutput(string $request): Message
    {
        $message = $this->getAiResponse($request);

        return $this->addInput($message, false);
    }

    /**
     * Get the response from the AI
     */
    public function getAiResponse($input)
    {
        if (!$input || $input == '') {
            return 'Please enter a message';
        }

        if (str_starts_with($input, '/image')) {
            return $this->getOpenAiImage();
        }

        return $this->getOpenAiChat();
    }

    /**
     * Get response chat from OpenAI
     */
    public function getOpenAiChat(int $limit = 5): string
    {
        $latestMessages = $this->messages()->latest()->limit($limit)->get()->sortBy('id');

        /**
         * Reverse the messages to preserve the order for OpenAI
         */
        $latestMessagesArray = [];
        foreach ($latestMessages as $message) {
            $latestMessagesArray[] = [
                'role' => $message->is_ai ? 'user' : 'assistant', 'content' => $message->compressed_body];
        }

        $response = OpenAI::chat()->create(['model' => 'gpt-3.5-turbo', 'messages' => $latestMessagesArray]);

        return $response->choices[0]->message->content;

    }

    /**
     * Generate title for the chat
     */
    public function generateTitle($message): string
    {
        $llm = getChatBuddyLLMProvider();

        $prompt = "Create only a single title from the text, it must not be more than 25 characters, keep the language spoken, here is the text: '$message'";

        $title = $llm->chat($prompt);

        $title = preg_replace('/[^A-Za-z0-9] /', '', $title);

        $this->title = $title;
        $this->save();

        return $title;
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Chat has many messages.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
