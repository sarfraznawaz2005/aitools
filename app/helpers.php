<?php
/*
 * legal advisor bot: I want you to act as a legal advisor, who can provide general guidance and insights on various legal matters, such as business law, contracts, intellectual property, or personal legal issues. While you can’t replace the need for professional legal counsel, you can help users understand basic legal concepts, provide information about legal rights and responsibilities, and offer guidance on when and how to seek legal help. My first request is ‘Explain the key elements that should be included in a basic business contract and their importance in protecting both parties involved.’
 * saas generator bot
 * landing page creator bot with ability to preview created page
 * AI based note-taking app
 * setup updator
 * Researcher Agent
 * UpWork AI extension
 * Image/Video Generator
 * https://www.youtube.com/watch?v=7rYR_l9oqQg
 *
 * Interesting PHP Libraries and Tools
 * https://github.com/cognesy/instructor-php
 * https://github.com/theodo-group/LLPhant
 * https://github.com/kambo-1st/langchain-php
 * */

use App\LLM\GeminiProvider;
use App\LLM\LlmProvider;
use App\LLM\OllamaProvider;
use App\LLM\OpenAiProvider;
use App\Models\ApiKey;
use App\Models\Bot;
use App\Models\Conversation;
use App\Services\StreamHandler;
use Illuminate\Support\Facades\Log;
use Native\Laravel\Facades\Settings;
use Native\Laravel\Facades\Window;
use Native\Laravel\Windows\PendingOpenWindow;

function getLLM(ApiKey $model): LlmProvider
{
    return match ($model->llm_type) {
        'Gemini' => new GeminiProvider($model->api_key, $model->model_name, ['maxOutputTokens' => 8192, 'temperature' => 1.0]),
        'OpenAI' => new OpenAiProvider($model->api_key, $model->model_name, ['max_tokens' => 4096, 'temperature' => 0.7]),
        default => new OllamaProvider($model->api_key, $model->model_name, ['max_tokens' => 4096]),
    };
}

function hasApiKeysCreated()
{
    return ApiKey::hasApiKeys();
}

function getSelectedLLMModel(string $key): ApiKey
{
    $selectedModel = Settings::get($key . '.selectedModel');

    if ($selectedModel && ApiKey::where('model_name', $selectedModel)->exists()) {
        $model = ApiKey::where('model_name', $selectedModel)->first();
    } else {
        $model = ApiKey::whereActive()->first();
    }

    return $model;
}

function getSelectedLLMProvider(string $key): LlmProvider
{
    return getLLM(getSelectedLLMModel($key));
}

function AIChatFailed($result): string
{
    if (str_contains(strtolower($result), 'failed to get a successful response') ||
        str_contains(strtolower($result), 'unknown error')) {
        return "There was some problem. $result";
    }

    return '';
}

function makePromptForTextBot(Bot $bot, string $userQuery, string $conversationHistory, int $version = 1): string
{
    $relatedQuestionsPrompt = '';

    if ($bot->showRelatedQuestions()) {
        $relatedQuestionsPrompt = config('prompts.textBotRelatedQuestionsPrompt');
    }

    $prompt = config("prompts.v$version");

    $prompt = str_ireplace('{{USER_QUESTION}}', $userQuery, $prompt);
    $prompt = str_ireplace('{{PROMPT}}', $bot->prompt, $prompt);
    $prompt = str_ireplace('{{CONVERSATION_HISTORY}}', $conversationHistory, $prompt);

    $prompt .= $relatedQuestionsPrompt;
    $prompt .= "\nPlease provide answer here:";

    if (app()->environment('local')) {
        Log::info("\n" . str_repeat('-', 100) . "\n" . $prompt . "\n");
    }

    return $prompt;
}

function makePromoptForDocumentBot(Bot $bot, string $infoHeader, string $context, string $userQuery, string $conversationHistory): string
{
    $relatedQuestionsPrompt = '';

    if ($bot->showRelatedQuestions()) {
        $relatedQuestionsPrompt = config('prompts.documentBotRelatedQuestionsPrompt');
    }

    $prompt = config('prompts.documentBotPrompt');

    $prompt = str_ireplace('{{INFO}}', $infoHeader, $prompt);
    $prompt = str_ireplace('{{CONTEXT}}', $context, $prompt);
    $prompt = str_ireplace('{{USER_QUESTION}}', $userQuery, $prompt);
    $prompt = str_ireplace('{{CONVERSATION_HISTORY}}', $conversationHistory, $prompt);

    $prompt .= $relatedQuestionsPrompt;
    $prompt .= "\nPlease provide answer here:";

    if (app()->environment('local')) {
        Log::info("\n" . str_repeat('-', 100) . "\n" . $prompt . "\n");
    }

    return $prompt;
}

function sendStream($text, $sendCloseSignal = false): void
{
    $streamHandler = StreamHandler::getInstance();

    $streamHandler->sendStream($text, $sendCloseSignal);
}

function htmlToText($html, $removeWhiteSpace = true): string
{
    // Replace <br> tags with newlines
    $text = preg_replace('/<br\s*\/?>/i', "\n", $html);

    // Replace </p> tags with double newlines
    $text = preg_replace('/<\/p>/i', "\n\n", $text);

    // Remove all remaining HTML tags
    $text = strip_tags($text);

    // Decode HTML entities
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Normalize line breaks
    $text = preg_replace('/\r\n|\r/', "\n", $text);

    // Replace any combination of more than two newlines and whitespace with two newlines
    $text = preg_replace('/(\s*\n\s*){3,}/', "\n\n", $text);

    // Remove extra whitespace
    if ($removeWhiteSpace) {
        $text = preg_replace('/\s+/', ' ', $text);
    }

    return trim($text);
}

function getBotIcon(Conversation $conversation = null): string
{
    return $conversation?->bot?->icon ?? '🤖';
}

function getBotName(Conversation $conversation = null): string
{
    return $conversation?->bot?->name ?? 'General';
}

function openWindow(
    string $id,
    string $route,
    array  $routeParams = [],
    bool   $focusable = true,
    bool   $closable = true,
    bool   $minimizable = true,
    bool   $maximizable = true,
    int    $width = 1280,
    int    $height = 800
): PendingOpenWindow
{
    return Window::open($id)
        ->route($route, $routeParams)
        ->showDevTools(false)
        //->frameless()
        //->titleBarHidden()
        //->fullscreen(true)
        ->width($width)
        ->hideMenu()
        ->minWidth($width)
        ->height($height)
        ->minHeight($height)
        ->lightVibrancy()
        ->hasShadow()
        //->rememberState()
        ->focusable($focusable)
        ->closable($closable)
        ->minimizable($minimizable)
        ->maximizable($maximizable);
}

function closeWindow(string $id): void
{
    Window::close($id);
}
