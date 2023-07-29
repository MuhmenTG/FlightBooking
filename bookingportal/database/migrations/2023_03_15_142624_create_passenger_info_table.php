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
        Schema::create('passenger_info', function (Blueprint $table) {
            $table->id();
            // Burde den her ikke være unik I DB'en?
            $table->string('bookingReference');
            // Hvorfor er starter den her med stort?
            $table->integer('PaymentInfoId');
            $table->string('title');
            $table->string('firstName');
            $table->string('lastName');
            // Hvorfor er det her ikke et timestamp?
            $table->string('dateOfBirth');
            $table->string('email');
            $table->string('passengerType');
            $table->string('ticketNumber');
            $table->boolean('isCancelled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passenger_info');
    }
};
