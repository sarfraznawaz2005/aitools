<?php

use App\LLM\OpenAiProvider;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('test', function () {
    $gemini = new OpenAiProvider(
        'sk-proj-B1O2oQox3DVJqdRrmDADT3BlbkFJgfuv6sxQvXr45eSBVZEY',
        'gpt-3.5-turbo',
        //['maxOutputTokens' => 8192, 'temperature' => 2.0]
    );

    echo $gemini->chat('tell me a story', true);
});
