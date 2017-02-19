<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTableStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('states');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        echo "Don't know how to revert";
    }
}
