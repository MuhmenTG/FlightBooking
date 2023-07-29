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
        Schema::create('airline', function (Blueprint $table) {
            $table->id();
            // Er det nødvendigt at ha airline med i navnet på kolonnen med?
            $table->string('airlineName');
            $table->string('IataDesignator');
            $table->string('threeDigitAirlineCode');
            $table->string('IataCode');
            $table->string('country');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline');
    }
};
