<?php

use Illuminate\Support\Facades\Route;
use Package\DocTalk\Http\Controllers\DocTalkController;

Route::group(
    [
        'prefix' => config('doctalk.path', 'doctalk'),
    ],
    function () {
        Route::get('/', [DocTalkController::class, 'index'])->name('doctalk.index');
    }
);

