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
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email');
            $table->string('password');
            $table->boolean('emailConfirmation')->default(false);
            $table->string('status');
            $table->boolean('isAgent')->nullable();
            $table->boolean('isAdmin')->nullable();
            $table->integer('firstTimeLoggedIn')->default(0);
            $table->timestamp('registeredAt')->useCurrent();
            $table->integer('deactivatedAt')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accounts');
    }
};
