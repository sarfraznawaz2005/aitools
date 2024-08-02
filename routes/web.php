<?php

use App\Livewire\ChatBuddy;
use App\Livewire\TextStyler;
use App\Livewire\TipsNotifier;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('test', function () {
    return view('test');
});

Route::get('chat-buddy', ChatBuddy::class)->name('chat-buddy');
Route::get('text-styler', TextStyler::class)->name('text-styler');
Route::get('tips-notifier', TipsNotifier::class)->name('tips-notifier');
