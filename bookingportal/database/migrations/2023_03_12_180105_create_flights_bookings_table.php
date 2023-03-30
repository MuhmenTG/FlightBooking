<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flights_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('bookingReference');
            $table->string('airline');
            $table->string('flightNumber');
            $table->string('departureFrom');
            $table->string('departureTerminal');
            $table->string('departureDateTime');
            $table->string('arrivelTo');
            $table->string('arrivelTerminal')->nullable();
            $table->string('arrivelDate');
            $table->string('flightDuration');
            $table->string('cabin')->nullable();
            $table->string('fareBasis')->nullable();
            $table->string('includedCheckedBags')->nullable();
            $table->boolean('isBookingConfirmed');
            $table->boolean('isPaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights_bookings');
    }
};