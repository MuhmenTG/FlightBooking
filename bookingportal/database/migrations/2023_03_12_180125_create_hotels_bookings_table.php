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
            $table->string('hotelBookingReference');
            $table->date('issueDate');
            $table->string('hotelName');
            $table->string('hotelLocation');
            $table->string('hotelCity');
            $table->string('hotelContact');
            $table->string('checkInDate');
            $table->string('checkOutDate');
            $table->string('roomType');
            $table->string('mainGuestFirstName');
            $table->string('mainGuestLasName');
            $table->string('mainGuestEmail');
            $table->integer('numberOfAdults');
            $table->integer('numberOfChildren')->nullable()->default(0);
            $table->string('policiesCheckInOutCheckIn')->nullable()->default(Null);
            $table->string('policiesCheckInOutCheckOut')->nullable()->default(Null);;
            $table->string('policiesCancellationDeadline')->nullable()->default(Null);
            $table->text('description');
            $table->string('paymentInfoId');
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
