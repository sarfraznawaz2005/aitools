<?php

use App\Livewire\Pages\ChatBuddy;
use App\Livewire\Pages\TextStyler;
use App\Livewire\Pages\TipsNotifier;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

$tools = config('tools');

Route::get('/', function () {
    return view('welcome');
})->name('home');

//Route::get('/run-scheduler', function () {
//    Artisan::call('schedule:run');
//});

Route::get('test', function () {
    return view('test');
})->name('test');

Route::get($tools['chat-buddy']['route'], ChatBuddy::class)->name($tools['chat-buddy']['route']);
Route::get($tools['chat-buddy']['route'] . '/{conversation}', ChatBuddy::class)->name($tools['chat-buddy']['route'] . 'load-conversation');
Route::get('/chat-buddy/chat/{conversation}', [ChatBuddy::class, 'chat']);

Route::get($tools['text-styler']['route'], TextStyler::class)->name($tools['text-styler']['route']);
Route::get('/text-styler/chat', [TextStyler::class, 'chat']);

Route::get($tools['tips-notifier']['route'], TipsNotifier::class)->name($tools['tips-notifier']['route']);
