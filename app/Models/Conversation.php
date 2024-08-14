<?php

namespace App\Models;

use App\Constants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Sajadsdi\LaravelSettingPro\Support\Setting;

class Conversation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = ['title'];

    protected static function boot(): void
    {
        parent::boot();

        self::deleteOlderConversations();
    }

    public function addChatMessage(string $message, bool $isAi = false): Message
    {
        // update conversation last used time
        $this->updated_at = now();
        $this->save();

        return $this->messages()->create([
            'body' => nl2br($message),
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
        25 characters and without punctuation characters, language must be same as Text. Text: '$message'
        ";

        $title = $llm->chat($prompt);

        $title = preg_replace('/[^A-Za-z0-9] /', '', $title);

        $this->title = $title;
        $this->updated_at = now();
        $this->created_at = now();
        $this->save();

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
            'llm' => $selectedModel->model_name . ' (' . $selectedModel->llm_type . ')',
            'conversation_id' => $this->id,
            'is_ai' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function deleteOlderConversations(): void
    {
        $days = 30;

        if (Setting::select('ChatBuddy')->has('chatBuddyDeleteOldDays')) {
            $days = Setting::select('ChatBuddy')->get('chatBuddyDeleteOldDays');
        }

        $oldConversations = static::query()
            ->where('created_at', '<', now()->subDays($days))
            ->where('favorite', false);

        if ($oldConversations->exists()) {
            $deletedCount = $oldConversations->delete();

            Log::info("Deleted $deletedCount old conversations");
        }
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
