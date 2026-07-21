<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AssetRepositoryInterface;
use App\Repositories\AssetRepository;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\CompanyRepository;
use App\Services\OCR\Contracts\OcrServiceInterface;
use App\Services\OCR\Providers\TesseractOcrProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\ThirdPartyRepositoryInterface::class,
            \App\Repositories\ThirdPartyRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\EmployeeRepositoryInterface::class,
            \App\Repositories\EmployeeRepository::class
        );
        $this->app->bind(AssetRepositoryInterface::class, AssetRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(\App\Repositories\Contracts\ProductRepositoryInterface::class, \App\Repositories\ProductRepository::class);
        $this->app->bind(\App\Repositories\Contracts\WarehouseRepositoryInterface::class, \App\Repositories\WarehouseRepository::class);
        $this->app->bind(\App\Repositories\Contracts\PurchaseRepositoryInterface::class, \App\Repositories\PurchaseRepository::class);
        $this->app->bind(\App\Repositories\Contracts\SaleRepositoryInterface::class, \App\Repositories\SaleRepository::class);
        $this->app->bind(OcrServiceInterface::class, TesseractOcrProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Limite global para a API (60 requests por minuto por IP ou utilizador)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Limite rigoroso para rotas de Autenticação / Login (5 tentativas por minuto)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Implicitly grant "Super Admin" role all permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
