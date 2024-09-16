<?php
/*
 *
 *
 * have a CSV file that you want to quickly query and visualize (https://supabase.com/blog/postgres-new)
 * wikipedia research via tool calling - https://github.com/google-gemini/cookbook/blob/main/examples/Search_reranking_using_embeddings.ipynb
 * function calling - https://github.com/google-gemini/cookbook/blob/main/quickstarts/rest/Function_calling_REST.ipynb
 * Setup proper roles when prompting AI? (https://github.com/google-gemini/cookbook/blob/main/quickstarts/rest/System_instructions_REST.ipynb)
 * json output -  (gemini - https://github.com/google-gemini/cookbook/blob/main/quickstarts/rest/JSON_mode_REST.ipynb | https://github.com/google-gemini/cookbook/blob/main/examples/json_capabilities/Text_Classification.ipynb)
 * Automatic hotel ordering handled by AI - https://github.com/google-gemini/cookbook/blob/main/examples/Agents_Function_Calling_Barista_Bot.ipynb
 * empty title on icon hover
 * setup updator
 * GraphRAG
 * Researcher Agent
 * UpWork AI extension
 * Image/Video Generator
 * https://www.youtube.com/watch?v=7rYR_l9oqQg
 *
 * Interesting PHP Libraries and Tools
 * https://github.com/cognesy/instructor-php
 * https://github.com/theodo-group/LLPhant
 * https://github.com/kambo-1st/langchain-php
 * https://github.com/BenSampo/laravel-embed
 * */

use App\Constants;
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
use Smalot\PdfParser\Config;
use Smalot\PdfParser\Parser;
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

    if ($selectedModel && ApiKey::query()->where('model_name', $selectedModel)->exists()) {
        $model = ApiKey::query()->where('model_name', $selectedModel)->first();
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
    if (
        str_contains(strtolower($result), 'failed to get a successful response') ||
        str_contains(strtolower($result), 'unknown error')
    ) {
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

function makePromoptForNotes(string $context, string $userQuery, string $conversationHistory): string
{
    // documentBotRelatedQuestionsPrompt looks good for notes too
    $relatedQuestionsPrompt = config('prompts.documentBotRelatedQuestionsPrompt');

    $prompt = config('prompts.notesPrompt');

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

function makePromptQuickChat(string $userQuery, string $conversationHistory, int $version = 1): string
{
    $relatedQuestionsPrompt = config('prompts.textBotRelatedQuestionsPrompt');

    $generalBotPrompt = Bot::query()->where('name', 'General')->first()->prompt;

    $prompt = config("prompts.v$version");

    $prompt = str_ireplace('{{USER_QUESTION}}', $userQuery, $prompt);
    $prompt = str_ireplace('{{PROMPT}}', $generalBotPrompt, $prompt);
    $prompt = str_ireplace('{{CONVERSATION_HISTORY}}', $conversationHistory, $prompt);

    $prompt .= $relatedQuestionsPrompt;
    $prompt .= "\nPlease provide answer here:";

    if (app()->environment('local')) {
        Log::info("\n" . str_repeat('-', 100) . "\n" . $prompt . "\n");
    }

    return $prompt;
}

function getMessages(array $messages): array
{
    $uniqueMessages = [];

    usort($messages, function ($a, $b) {
        return $a['timestamp'] - $b['timestamp'];
    });

    // Remove duplicates and empty content entries, and filter by role and content
    $messages = array_values(array_filter(array_unique($messages, SORT_REGULAR), function ($item) {
        $filterOutStrings = [
            "conversation history",
            "have enough information to answer this question accurately",
            "provided context"
        ];

        if ($item['role'] !== 'user') {
            foreach ($filterOutStrings as $str) {
                if (str_contains(strtolower($item['content']), strtolower($str))) {
                    return false;
                }
            }
        }

        // Also ensure that the content is not empty
        return !empty($item['content']);
    }));

    // Format and filter unique messages
    foreach ($messages as $message) {
        $formattedMessage = ($message['role'] === 'user' ? 'USER: ' : 'AI: ') . $message['content'];

        if ($message['role'] === 'user') {
            $uniqueMessages[] = $formattedMessage; // allow all user messages
        } else {
            if (!in_array($formattedMessage, $uniqueMessages)) {
                $uniqueMessages[] = htmlToText($formattedMessage);
            }
        }
    }

    return array_slice($uniqueMessages, 0, Constants::NOTES_TOTAL_CONVERSATION_HISTORY);
}

function sendStream($text, $sendCloseSignal = false): void
{
    $streamHandler = StreamHandler::getInstance();

    $streamHandler->sendStream($text, $sendCloseSignal);
}

function htmlToText($html, $removeWhiteSpace = true): string
{
    $text = str_ireplace('related questions:', '', $html);

    // Remove <related_question> tags including their contents
    $text = preg_replace('/<related_question>.*?<\/related_question>/is', '', $text);
    $text = preg_replace('/&lt;related_question&gt;.*?&lt;\/related_question&gt;/is', '', $text);

    // Replace <br> tags with newlines
    $text = preg_replace('/<br\s*\/?>/i', "\n", $text);

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

function openWindow(
    string $id,
    string $route,
    array  $routeParams = [],
    bool   $focusable = true,
    bool   $closable = true,
    bool   $minimizable = true,
    bool   $maximizable = true,
    int    $width = 1280,
    int    $height = 800,
    string $title = ''
): PendingOpenWindow {
    return Window::open($id)
        ->title(config('app.name') . ($title ? ' - ' . $title : ''))
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

function processMarkdownToHtml($markdownContent, $fixBroken = true): string
{
    // Use the MarkdownRenderer to convert markdown to HTML
    $markdownRenderer = app(MarkdownRenderer::class);
    $htmlContent = $markdownRenderer->toHtml($markdownContent);

    if ($fixBroken) {
        // Suppress libxml errors and warnings
        libxml_use_internal_errors(true);

        // Initialize DOMDocument and prevent automatic DOCTYPE addition
        $doc = new DOMDocument();
        $doc->substituteEntities = false;

        // Convert to HTML entities and load into DOMDocument with a dummy structure
        $content = mb_convert_encoding($htmlContent, 'html-entities', 'utf-8');
        $success = $doc->loadHTML('<html><body>' . $content . '</body></html>');

        libxml_clear_errors();

        if ($success) {
            // Extract only the content inside the <body> tag
            $bodyContent = '';
            foreach ($doc->getElementsByTagName('body')->item(0)->childNodes as $childNode) {
                $bodyContent .= $doc->saveHTML($childNode);
            }

            return $bodyContent ?: $htmlContent;
        }
    }

    return $htmlContent;
}

/**
 * @throws Exception
 */
function extractTextFromFile(string $file): array
{
    $extension = pathinfo($file, PATHINFO_EXTENSION);

    switch (strtolower($extension)) {
        case 'pdf':

            $config = new Config();
            $config->setRetainImageContent(false);

            $parser = new Parser([], $config);

            $text = [];
            $pdf = $parser->parseFile($file);
            $pages = $pdf->getPages();

            foreach ($pages as $pageNumber => $page) {
                $text[] = [
                    'text' => $page->getText(),
                    'source' => basename($file) . '[' . $pageNumber + 1 . ']',
                ];
            }

            //file_put_contents('pdf_text', json_encode($text, JSON_PRETTY_PRINT));
            return $text;
        case 'txt':
        case 'md':
        case 'html':
        case 'htm':

            $content = file_get_contents($file);
            $lines = explode("\n", $content);

            $text[] = [
                'text' => $lines,
                'source' => basename($file),
            ];

            return $text;
        default:
            throw new Exception("Unsupported file type: $extension");
    }
}

function out($data): void
{
    file_put_contents(storage_path('logs/laravel.log'), '');

    info($data);
}
