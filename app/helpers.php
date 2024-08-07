<?php
/*
 * TODO
 * global disabler until api key is saved
 * loading indicator for chat list and sidebar and other components
 * download conversation as pdf
 * chat with pdf
 * */

use App\Models\ApiKey;
use Sajadsdi\LaravelSettingPro\Support\Setting;

function hasApiKeysCreated()
{
    return ApiKey::hasApiKeys();
}

function getChatBuddySelectedModel(): string
{
    if (
        Setting::select('ChatBuddy')->has('selectedModel') &&
        ApiKey::where('model_name', Setting::select('ChatBuddy')->get('selectedModel'))->exists()
    ) {
        return ApiKey::where('model_name', Setting::select('ChatBuddy')->get('selectedModel'))->first();
    }

    return ApiKey::whereActive()->first();
}
