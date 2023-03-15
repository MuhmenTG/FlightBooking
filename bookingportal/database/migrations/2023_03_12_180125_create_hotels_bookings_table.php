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
        Schema::create('hotels_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('hotelBookingReference')->unique();
            $table->date('issueDate');
            $table->string('hotelName');
            $table->string('hotelLocation');
            $table->string('hotelCity');
            $table->string('hotelContact');
            $table->date('checkInDate');
            $table->date('checkOutDate');
            $table->string('roomType');
            $table->string('mainGuest');
            $table->integer('numberOfAdults');
            $table->integer('numberOfChildren');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels_bookings');
    }
};
