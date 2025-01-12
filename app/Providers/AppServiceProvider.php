<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ContaRepositoryInterface;
use App\Repositories\SaldoContaRepositoryInterface;
use App\Repositories\TransacaoRepositoryInterface;
use App\Repositories\ContaRepository;
use App\Repositories\SaldoContaRepository;
use App\Repositories\TransacaoRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ContaRepositoryInterface::class, ContaRepository::class);
        $this->app->bind(SaldoContaRepositoryInterface::class, SaldoContaRepository::class);
        $this->app->bind(TransacaoRepositoryInterface::class, TransacaoRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
