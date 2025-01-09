<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ContaController;
use App\Http\Controllers\OperacaoBancariaController;

Route::prefix('/contas')->group(function () {
    Route::post('/', [ContaController::class, 'store'])->name('conta.store');
    Route::get('/', [ContaController::class, 'index'])->name('conta.index');
});

Route::prefix('/operacoes-bancarias')->group(function () {
    Route::get('/', [OperacaoBancariaController::class, 'index'])->name('operacao.index');
    Route::get('/saldo/{contas_id}/{moeda?}', [OperacaoBancariaController::class, 'saldo'])->name('operacao.saldo');
    Route::put('/deposito', [OperacaoBancariaController::class, 'deposito'])->name('operacao.deposito');
    Route::put('/saque', [OperacaoBancariaController::class, 'saque'])->name('operacao.saque');
});