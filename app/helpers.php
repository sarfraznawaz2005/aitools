<?php
/*
 * TODO
 * global disabler until api key is saved
 * send message on enter
 * loading indicator for chat list and sidebar and other components
 * make sure there are no errors on console on all pages
 * download conversation as pdf
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

function getChatBuddyLLMProvider(): LlmProvider
{
    if (
        Setting::select('ChatBuddy')->has('selectedModel') &&
        ApiKey::where('model_name', Setting::select('ChatBuddy')->get('selectedModel'))->exists()
    ) {
        $model = ApiKey::where('model_name', Setting::select('ChatBuddy')->get('selectedModel'))->first();
    } else {
        $model = ApiKey::whereActive()->first();
    }

    return getLLM($model);
}
