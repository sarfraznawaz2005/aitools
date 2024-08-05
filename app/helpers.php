<?php

use App\Models\ApiKey;

function getActiveModel(): string
{
    if (session('selectedModel') && ApiKey::where('model_name', session('selectedModel'))->exists()) {
        return ApiKey::where('model_name', session('selectedModel'))->first();
    }

    return ApiKey::where('active', true)->first();
}
