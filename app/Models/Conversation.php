<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function addChatMessage(string $message, bool $isAi = false): Message
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
     * Generate title for the chat
     */
    public function generateTitle($message): string
    {
        $llm = getChatBuddyLLMProvider();

        $prompt = "
        Create only a single title from the text, it must not be more than 25 characters and without punctuation characters,
        keep the language spoken, here is the text: '$message'
        ";

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
