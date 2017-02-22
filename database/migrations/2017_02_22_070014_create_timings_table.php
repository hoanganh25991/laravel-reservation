<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('session_id');
            $table->dateTime('first_arrival');
            $table->dateTime('last_arrival');
            $table->tinyInteger('sitter_capacity_1');
            $table->tinyInteger('sitter_capacity_2');
            $table->tinyInteger('sitter_capacity_3_4');
            $table->tinyInteger('sitter_capacity_5_6');
            $table->tinyInteger('sitter_capacity_7_x');
            $table->tinyInteger('max_table_size');
            $table->boolean('children_allowed');
            $table->tinyInteger('state');
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
        Schema::dropIfExists('timings');
    }
}
