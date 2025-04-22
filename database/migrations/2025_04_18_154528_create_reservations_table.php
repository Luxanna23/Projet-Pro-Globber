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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->float('price');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'accepted', 'refused', 'cancelled'])->default('pending');
            $table->foreignId('annonce_id')->constrained();
            $table->foreignId('calendrier_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
