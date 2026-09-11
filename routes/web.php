<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Jeremykenedy\LaravelDarkmodeToggle\Http\Controllers\DarkmodeController;

Route::group([
    'prefix'     => config('darkmode.routes.prefix', 'darkmode'),
    'middleware' => config('darkmode.routes.middleware', ['web', 'auth']),
], function () {
    Route::put('/preference', [DarkmodeController::class, 'update'])->name('darkmode.update');
});
