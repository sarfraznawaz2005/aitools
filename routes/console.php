<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('test', function () {
    $llm = getChatBuddyLLMProvider();

    $title = $llm->chat(
        'Create only a single title from the text, it should not be more than 25 characters, keep the language spoken: Hello World'
    );

    echo $title;
});
