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
        Schema::create('tv_display_times', function (Blueprint $table) {
            // for every day start time
            $table->id();
            $table->unsignedBigInteger('tv_id');
            $table->foreign('tv_id')->references('id')->on('tvs')
                ->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->date('date'); // Start time for displaying ads
            $table->time('start_time'); // Start time for displaying ads
            $table->time('end_time');   // End time for displaying ads
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_display_times');
    }
};
