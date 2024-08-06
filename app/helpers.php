<?php
/*
 * TODO
 * global disabler until api key is saved
 * chat with pdf
 * */

use App\Models\ApiKey;
use Sajadsdi\LaravelSettingPro\Support\Setting;

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
