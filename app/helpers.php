<?php
/*
 * openai chunks issue
 * prompt format issue
 * metadata duplication
 * smaller chunks for notes
 * notes settings?
 * related questions broken html sometimes, should use strucutred output machanism
 * backup to one drive, etc
 * empty title on icon hover
 * setup updator
 * Setup proper roles when prompting AI?
 * Researcher Agent
 * UpWork AI extension
 * Image/Video Generator
 * GraphRAG
 * https://www.youtube.com/watch?v=7rYR_l9oqQg
 *
 * Interesting PHP Libraries and Tools
 * https://github.com/cognesy/instructor-php
 * https://github.com/theodo-group/LLPhant
 * https://github.com/kambo-1st/langchain-php
 * https://github.com/BenSampo/laravel-embed
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
use Spatie\LaravelMarkdown\MarkdownRenderer;

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

function fetchUrlContent($url): bool|string
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36 Edg/128.0.0.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

    // Optional: Set headers if needed (e.g., for APIs)
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //     'Accept: application/json',
    //     'Content-Type: application/json',
    // ));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        Log::error(curl_error($ch));
        curl_close($ch);
        return false;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check if the response was successful (status code 200-299)
    if ($httpCode >= 200 && $httpCode < 300) {
        return $response;
    } else {
        Log::error('HTTP error: ' . $httpCode);
        return false;
    }
}

function processMarkdownToHtml($markdownContent): string
{
    $markdownRenderer = app(MarkdownRenderer::class);
    $htmlContent = $markdownRenderer->toHtml($markdownContent);

    // Fix any remaining broken HTML and ensure UTF-8 encoding
    libxml_use_internal_errors(true); // Suppress libxml errors and warnings

    $doc = new DOMDocument();
    $doc->substituteEntities = false;
    $content = mb_convert_encoding($htmlContent, 'html-entities', 'utf-8');
    $success = $doc->loadHTML($content);

    libxml_clear_errors();

    if ($success) {
        $fixedHtml = $doc->saveHTML();
        return $fixedHtml !== false ? $fixedHtml : $htmlContent;
    }

    return $htmlContent;
}


