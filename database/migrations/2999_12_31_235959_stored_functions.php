<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class StoredFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    //    DB::unprepared( file_get_contents( dirname( __DIR__ ) . '/stored/create_stored_statements.sql' ) );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    //    DB::unprepared( file_get_contents( dirname( __DIR__ ) . '/stored/drop_stored_statements.sql' ) );
    }
}
