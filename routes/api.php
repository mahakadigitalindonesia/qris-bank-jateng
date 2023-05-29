<?php

use Illuminate\Support\Facades\Route;
use Mdigi\QrisBankJateng\Http\Controllers\Api\QrisController;

Route::prefix(config('qris.route_prefix'))
    ->group(function () {
        Route::prefix('qris')
            ->as('qris.')
            ->group(function () {
                Route::get('/{nop}', QrisController::class)
                    ->name('nop');
            });
    });