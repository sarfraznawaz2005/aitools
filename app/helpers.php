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
