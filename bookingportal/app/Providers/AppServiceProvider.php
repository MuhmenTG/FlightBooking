<?php

namespace App\Providers;

use App\Mail\IEmailService;
use App\Mail\ISendEmailService;
use App\Mail\SendEmailService;
use App\Repositories\TravelAgentRepository;
use App\Services\Amadeus\AmadeusService;
use App\Services\Amadeus\IAmadeusService;
use App\Services\Authentication\AuthenticationService;
use App\Services\Authentication\IAuthenticationService;
use App\Services\BackOffice\BackOfficeService;
use App\Services\BackOffice\IBackOfficeService;
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

        $this->app->bind(TravelAgentRepository::class, function ($app) {
            return new TravelAgentRepository();
        });

        $this->app->bind(IAmadeusService::class, AmadeusService::class);

        $this->app->bind(IBookingService::class, BookingService::class);

        $this->app->bind(IPaymentService::class, PaymentService::class);

        $this->app->bind(IBackOfficeService::class, BackOfficeService::class);

        $this->app->bind(IAuthenticationService::class, AuthenticationService::class);

        $this->app->bind(ISendEmailService::class, SendEmailService::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
