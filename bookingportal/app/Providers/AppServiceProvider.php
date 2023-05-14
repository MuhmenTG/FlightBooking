<?php

namespace App\Providers;

use App\Repositories\BackOfficeRepository;
use App\Repositories\BookingRepository;
use App\Repositories\TravelAgentRepository;
use App\Services\BackOfficeService;
use App\Services\BookingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(BackOfficeRepository::class, function ($app) {
            return new BackOfficeRepository();
        });
    
        $this->app->bind(BackOfficeService::class, function ($app) {
            return new BackOfficeService($app->make(BackOfficeRepository::class));
        });

        $this->app->bind(TravelAgentRepository::class, function ($app) {
            return new TravelAgentRepository();
        });
    
        $this->app->bind(BookingService::class, function ($app) {
            return new BookingService($app->make(BookingService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
