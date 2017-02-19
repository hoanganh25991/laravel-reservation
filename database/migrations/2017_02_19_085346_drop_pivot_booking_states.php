<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPivotBookingStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_state', function(Blueprint $table){
            $table->dropForeign('booking_state_booking_id_foreign');
            $table->dropForeign('booking_state_state_id_foreign');
        });
        Schema::dropIfExists('booking_state');

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
