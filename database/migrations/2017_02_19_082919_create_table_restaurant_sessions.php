<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRestaurantSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_sessions', function (Blueprint $table) {
            $table->increments('id');
            //name
            $table->string('name')->default('session');
            //date
            $table->timestamp('day')->nullable(false);
            //fisrt arrival
            $table->time('first_arrival')->nullable(false);
            //last arrival
            $table->time('last_arrival')->nullable(false);
            //interval, in minute
            $table->integer('interval')->default(30);
            //num sitter type 1
            $table->integer('sitter_1_capacity')->nullable(false);
            $table->integer('sitter_2_capacity')->nullable(false);
            $table->integer('sitter_3_4_capacity')->nullable(false);
            $table->integer('sitter_5_6_capacity')->nullable(false);
            $table->integer('sitter_7_x_capacity')->nullable(false);
            $table->tinyInteger('children_allowed')->default(1);

            $table->integer('type')->default(0);

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
        Schema::dropIfExists('restaurant_sessions');
    }
}
