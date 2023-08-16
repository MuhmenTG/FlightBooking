<?php

namespace App\Providers;

use App\Mail\IEmailService;
use App\Mail\ISendEmailService;
use App\Mail\SendEmailService;
use App\Repositories\BackOfficeRepository;
use App\Repositories\IBackOfficeRepository;
use App\Repositories\ITravelAgentRepository;
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
use Illuminate\Database\Eloquent\Builder;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(IAmadeusService::class, AmadeusService::class);

        $this->app->bind(IBookingService::class, BookingService::class);

        $this->app->bind(IPaymentService::class, PaymentService::class);

        $this->app->bind(IBackOfficeService::class, BackOfficeService::class);

        $this->app->bind(IAuthenticationService::class, AuthenticationService::class);

        $this->app->bind(ISendEmailService::class, SendEmailService::class);

        $this->app->bind(IBackOfficeRepository::class, BackOfficeRepository::class);

        $this->app->bind(ITravelAgentRepository::class, TravelAgentRepository::class);



    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Builder::macro('whereLike', function(string $column, string $search) {
            return $this->orWhereRaw("LEFT($column, 3) = ?", [$search]);
         });         
    }
}
