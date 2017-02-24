<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesForReservation extends Migration
{
    const SQL_FILE_NAME = 'reservation.sql';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql_file_path = database_path(self::SQL_FILE_NAME);
        $query = file_get_contents($sql_file_path);
//        DB::statement($query);
        DB::unprepared($query);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $created_tables = [
            'customer',
            'customer_authentication',
            'outlet_reservation_setting',
            'outlet_reservation_user',
            'outlet_reservation_user_reset_password',
            'reservation',
            'session',
            'timing'
        ];

        foreach($created_tables as $table){
            Schema::dropIfExists($table);
        }
    }
}
