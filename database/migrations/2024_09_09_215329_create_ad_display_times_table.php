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
        Schema::create('ad_display_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ad_schedule_id');
            $table->foreign('ad_schedule_id')->references('id')->on('ad_schedules')
                ->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->date('display_date'); // Specific day the ad will be shown
            $table->time('display_time'); // Specific time the ad will be shown
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_display_times');
    }
};
