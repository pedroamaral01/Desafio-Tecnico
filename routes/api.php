<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ContaController;

Route::prefix('/contas')->group(function () {
    Route::post('/', [ContaController::class, 'store'])->name('conta.store');
    Route::get('/', [ContaController::class, 'index'])->name('conta.index');
});