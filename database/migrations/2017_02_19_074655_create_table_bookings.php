<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            //location
            $table->string('location')->nullable(false);
            //map outltet_id
            $table->unsignedBigInteger('outlet_id')->references('outlet')->on('id');
            //num adults
            $table->integer('num_adults')->default(0);
            //num children
            $table->integer('num_children')->default(0);
            //confirmation ID
            $table->string('confirmation_id')->unique();
            //name of user, who booking
            $table->string('user_first_name')->nullable(false);
            $table->string('user_surname')->nullable(false);
            //contact, phone
            $table->string('user_phone_number')->nullable(false);
            $table->string('user_email')->nullable(false);
            //map user
            $table->unsignedInteger('user_id')->references('users')->on('id');
            //custom message
            $table->mediumText('custom_message')->nullable(false);

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
        Schema::dropIfExists('bookings');
    }
}
