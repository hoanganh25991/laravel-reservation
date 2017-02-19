<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PivotBookingState extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_state', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings');

            $table->unsignedInteger('state_id');
            $table->foreign('state_id')->references('id')->on('states');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_state');
    }
}
