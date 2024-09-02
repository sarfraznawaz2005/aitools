<?php

namespace App\Models;

use App\Constants;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Native\Laravel\Facades\Settings;

class Conversation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'bot_id',
        'title',
    ];

    protected static function boot(): void
    {
        parent::boot();

        try {
            self::deleteOld();
        } catch (Exception) {
        }
    }

    public function addChatMessage(string $message, bool $isAi = false): Message
    {
        // update conversation last used time
        $this->updated_at = now();
        $this->save();

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
        $llm = getSelectedLLMProvider(Constants::CHATBUDDY_SELECTED_LLM_KEY);

        $prompt = "
        Create only a single title from the provided Text, it must be of minimum 4 characters and must not be more than
        25 characters and without punctuation characters, language must be same as Text. Text: '$message'. If text is
        too short, create on your own without completing the text.
        ";

        $title = 'New Conversation';

        try {
            $title = $llm->chat($prompt);
            $title = preg_replace('/[^A-Za-z0-9] /', '', $title);
        } catch (Exception) {
            //
        } finally {
            $this->title = $title;
            $this->updated_at = now();
            $this->created_at = now();
            $this->save();
        }

        return $title;
    }

    // Create temp answer to show the user that the AI is typing
    public function createTempAImessage(): void
    {
        // update conversation last used time
        $this->updated_at = now();
        $this->save();

        $selectedModel = getSelectedLLMModel(Constants::CHATBUDDY_SELECTED_LLM_KEY);

        $this->messages()->create([
            'body' => Constants::CHATBUDDY_LOADING_STRING,
            'llm' => $selectedModel->llm_type . ' (' . $selectedModel->model_name . ')',
            'conversation_id' => $this->id,
            'is_ai' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function deleteOld(): void
    {
        $days = Settings::get('ChatBuddy.chatBuddyDeleteOldDays', 30);

        $oldConversations = static::query()
            ->where('created_at', '<', now()->subDays($days))
            ->where('favorite', false)
            ->where('archived', false);

        if ($oldConversations->exists()) {
            $deletedCount = $oldConversations->delete();

            Log::info("Deleted $deletedCount old conversations.");
        }
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }
}
