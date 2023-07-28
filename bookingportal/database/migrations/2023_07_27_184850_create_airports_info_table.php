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
            
        Schema::create('airports_info', function (Blueprint $table) {
            $table->id();
            $table->string('airportIcao');
            $table->string('airportName');
            $table->string('city');
            $table->string('country');
        });      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airports_info');
    }
};
