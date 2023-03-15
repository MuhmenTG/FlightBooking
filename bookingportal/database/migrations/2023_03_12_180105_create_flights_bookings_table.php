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
            $table->string('bookingReference')->unique();
            $table->dateTime('issueDate');
            $table->string('airline');
            $table->string('flightNumber');
            $table->string('departureFrom');
            $table->dateTime('departureDateTime');
            $table->string('arrivelTo');
            $table->dateTime('arrivelDate');
            $table->string('flightDuration');
            $table->boolean('isBookingConfirmed');
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