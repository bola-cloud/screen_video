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
        Schema::create('ad_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertisement_id');
            $table->foreign('advertisement_id')->references('id')->on('advertisements')
                ->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->unsignedBigInteger('tv_id');
            $table->foreign('tv_id')->references('id')->on('tvs')
                ->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->integer('order');
            $table->date('date'); // Stores the start date of the schedule
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_schedules');
    }
};
