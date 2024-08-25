<?php
/*
 * TODO
 * pagination for tips
 * open window on notification click
 * Tips options
 * check all livewire events are working, might need to add "native:" prefix
 * chatbuddy - save embeddings into db? Might improve performance
 * chatbuddy - use https://github.com/theodo-group/LLPhant
 * pass role to llms for chatbudy? this might solve issue of doctor related questions
 * chatbudy - replace built-in prompt with user-defined prompts?
 * use nativephp Settings facade for diff settings instead of composer package
 * Researcher Agent
 * UpWork AI extension
 * Image/Video Generator
 * https://www.youtube.com/watch?v=7rYR_l9oqQg
 * */

use App\LLM\GeminiProvider;
use App\LLM\LlmProvider;
use App\LLM\OllamaProvider;
use App\LLM\OpenAiProvider;
use App\Models\ApiKey;
use App\Models\Bot;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;
use Native\Laravel\Facades\Window;
use Native\Laravel\Windows\PendingOpenWindow;
use Sajadsdi\LaravelSettingPro\Support\Setting;

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
    if (
        Setting::select($key)->has('selectedModel') &&
        ApiKey::where('model_name', Setting::select($key)->get('selectedModel'))->exists()
    ) {
        $model = ApiKey::where('model_name', Setting::select($key)->get('selectedModel'))->first();
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
    $output = "event: update\n";

    if (!$sendCloseSignal) {
        $output .= "data: " . json_encode($text) . "\n\n";
    } else {
        $output .= "data: <END_STREAMING_SSE>\n\n";
    }

    // Write to stdout for NativePHP
    $stream = fopen('php://stdout', 'w');
    fwrite($stream, $output);
    fflush($stream);

    // Echo for browser SSE
    echo $output;

    // Attempt to flush output buffers
    if (ob_get_level() > 0) {
        ob_flush();
    }

    flush();
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

function openWindow(string $id, string $route, array $params = [], $focusable = true, $closable = true, $minimizable = true, $maximizable = true): PendingOpenWindow
{
    return Window::open($id)
        ->route($route, $params)
        ->showDevTools(false)
        //->frameless()
        //->titleBarHidden()
        //->fullscreen(true)
        ->width(1280)
        ->hideMenu()
        ->minWidth(1024)
        ->height(800)
        ->minHeight(800)
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
