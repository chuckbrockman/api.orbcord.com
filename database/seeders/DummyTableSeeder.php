<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('mysql_data')->statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::connection('mysql_data')->table('dummy')->truncate();
        DB::connection('mysql_data')->statement('SET FOREIGN_KEY_CHECKS=1;');

        for( $i = 1; $i <= 31; $i++ ) {
            DB::connection('mysql_data')->table('dummy')->insert([ 'id' => $i ]);
        }
    }
}
