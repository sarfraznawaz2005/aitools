<?php

use App\Livewire\ChatBuddy;
use App\Livewire\TextStyler;
use App\Livewire\TipsNotifier;
use Illuminate\Support\Facades\Route;

$tools = config('tools');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('test', function () {
    return view('test');
});

Route::get($tools['chat-buddy']['route'], ChatBuddy::class)->name($tools['chat-buddy']['route']);
Route::get($tools['text-styler']['route'], TextStyler::class)->name($tools['text-styler']['route']);
Route::get($tools['tips-notifier']['route'], TipsNotifier::class)->name($tools['tips-notifier']['route']);
