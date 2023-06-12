<?php

namespace App\Providers;

use App\Repositories\BackOfficeRepository;
use App\Repositories\TravelAgentRepository;
use App\Services\Amadeus\AmadeusService;
use App\Services\Amadeus\IAmadeusService;
use App\Services\BackOfficeService;
use App\Services\Booking\BookingService;
use App\Services\Booking\IBookingService;
use App\Services\Payment\IPaymentService;
use App\Services\Payment\PaymentService;
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

        $this->app->bind(IAmadeusService::class, AmadeusService::class);

        $this->app->bind(IBookingService::class, BookingService::class);

        $this->app->bind(IPaymentService::class, PaymentService::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
