<?php

use App\LLM\OllamaProvider;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('test', function () {
    $llm = new OllamaProvider(
        'whatever',
        'qwen:latest',
    //['maxOutputTokens' => 8192, 'temperature' => 2.0]
    );

    echo $llm->chat('tell me a story');
});
