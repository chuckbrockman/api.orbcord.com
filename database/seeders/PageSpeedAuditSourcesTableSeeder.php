<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSpeedAuditSourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('mysql_data')->statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::connection('mysql_data')->table('page_speed_audit_sources')->truncate();
        DB::connection('mysql_data')->statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::connection('mysql_data')->table('page_speed_audit_sources')->insert([
            [
                'name' => 'Google Pagespeed',
                'code' => 'google_pagespeed',
            ],
            [
                'name' => 'GTMetrix',
                'code' => 'gtmetrix',
            ],
         ]);
    }
}
