<?php

use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('store', [SettingController::class, 'store'])->name('settings.store');

