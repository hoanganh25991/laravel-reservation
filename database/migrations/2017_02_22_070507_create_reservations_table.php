<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table');
            $table->text('dinner_note');
            $table->text('staff_note');
            $table->tinyInteger('status');
            $table->tinyInteger('party_size');
            $table->tinyInteger('dinner_tag');
            $table->tinyInteger('in_out_door');
            $table->boolean('send_confirmation_email');
            $table->boolean('read_state');

            $table->integer('booking_id');
            $table->integer('customer_id');

            $table->timestamp('date');

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
        Schema::dropIfExists('reservations');
    }
}
