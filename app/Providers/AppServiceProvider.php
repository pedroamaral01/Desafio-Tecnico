<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\RepositoryInterface;
use App\Repositories\ContaRepository;
use App\Repositories\SaldoContaRepository;
use App\Repositories\TransacaoRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(RepositoryInterface::class . '.conta', ContaRepository::class);
        $this->app->bind(RepositoryInterface::class . '.saldo', SaldoContaRepository::class);
        $this->app->bind(RepositoryInterface::class . '.transacao', TransacaoRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
