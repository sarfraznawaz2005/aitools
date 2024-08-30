<?php

use App\Livewire\Notes\NotesListing;
use App\Livewire\Pages\ChatBuddy;
use App\Livewire\Pages\SmartNotes;
use App\Livewire\Pages\TextStyler;
use App\Livewire\Pages\TipContentOutput;
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
Route::get('chat-buddy/{conversation}', ChatBuddy::class)->name('chat-buddy.loadconversation');
Route::get('chat-buddy/chat/{conversation}', [ChatBuddy::class, 'chat']);

Route::get('smart-notes', SmartNotes::class)->name('smart-notes');
Route::get('smart-notes/{folder}', NotesListing::class)->name('smart-notes.listing');

Route::get('text-styler', TextStyler::class)->name('text-styler');
Route::get('text-styler/chat', [TextStyler::class, 'chat']);

Route::get('tips-notifier', TipsNotifier::class)->name('tips-notifier');
Route::get('tip-content/{id}', TipContentOutput::class)->name('tip-content');
