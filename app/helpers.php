<?php
/*
 * TODO
 * global disabler until api key is saved
 * export as html, txt and markdown
 * loading indicator for chat list and sidebar and other components
 * make sure there are no errors on console on all pages
 * chat with pdf
 * get other tools idea from eteam ai
 * */

use App\LLM\GeminiProvider;
use App\LLM\LlmProvider;
use App\LLM\OllamaProvider;
use App\LLM\OpenAiProvider;
use App\Models\ApiKey;
use Sajadsdi\LaravelSettingPro\Support\Setting;

function getLLM(ApiKey $model): LlmProvider
{
    return match ($model->llm_type) {
        'Gemini' => new GeminiProvider($model->api_key, $model->model_name, ['maxOutputTokens' => 8192]),
        'OpenAI' => new OpenAiProvider($model->api_key, $model->model_name, ['max_tokens' => 4096]),
        default => new OllamaProvider($model->api_key, $model->model_name, ['max_tokens' => 4096]),
    };
}

function hasApiKeysCreated()
{
    return ApiKey::hasApiKeys();
}

function getChatBuddySelectedLLMModel(): ApiKey
{
    if (
        Setting::select('ChatBuddy')->has('selectedModel') &&
        ApiKey::where('model_name', Setting::select('ChatBuddy')->get('selectedModel'))->exists()
    ) {
        $model = ApiKey::where('model_name', Setting::select('ChatBuddy')->get('selectedModel'))->first();
    } else {
        $model = ApiKey::whereActive()->first();
    }

    return $model;
}

function getChatBuddyLLMProvider(): LlmProvider
{
    return getLLM(getChatBuddySelectedLLMModel());
}

function AIChatFailed($result): string
{
    if (str_contains(strtolower($result), 'failed to get a successful response') ||
        str_contains(strtolower($result), 'unknown error')) {
        return "There was some problem. $result";
    }

    return '';
}
