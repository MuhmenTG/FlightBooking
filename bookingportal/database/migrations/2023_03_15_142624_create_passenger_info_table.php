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
            $table->string('PNR');
            $table->integer('PaymentInfoId');
            $table->string('firstName');
            $table->string('lastName');
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
