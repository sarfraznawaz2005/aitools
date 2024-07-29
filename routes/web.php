<?php

use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('api_keys_form', [SettingController::class, 'submitApiKeysForm'])->name('submit.form.api_keys_form');

