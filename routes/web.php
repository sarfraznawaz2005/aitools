<?php

use App\Livewire\Pages\ChatBuddy;
use App\Livewire\Pages\TextStyler;
use App\Livewire\Pages\TipsNotifier;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

//Route::get('/run-scheduler', function () {
//    Artisan::call('schedule:run');
//});

Route::get('test', function () {
    return view('test');
})->name('test');

Route::get('chat-buddy', ChatBuddy::class)->name('chat-buddy');
Route::get('chat-buddy/{conversation}', ChatBuddy::class)->name('chat-buddyload-conversation');
Route::get('chat-buddy/chat/{conversation}', [ChatBuddy::class, 'chat']);

Route::get('text-styler', TextStyler::class)->name('text-styler');
Route::get('text-styler/chat', [TextStyler::class, 'chat']);

Route::get('tips-notifier', TipsNotifier::class)->name('tips-notifier');
Route::get('tip-content/{id}', [TipsNotifier::class, 'showContentWindow']);
