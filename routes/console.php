<?php

use App\LLM\GeminiProvider;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('test', function () {
    $gemini = new GeminiProvider(
        'AIzaSyCNZB_8GOXr8Vx9Y_Xn93PKzZ52F8DqbsY',
        'gemini-1.5-flash',
        ['maxOutputTokens' => 8192, 'temperature' => 2.0]
    );

    echo $gemini->complete('Pakistan is a country in');
});
