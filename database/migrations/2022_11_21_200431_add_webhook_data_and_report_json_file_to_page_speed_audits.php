<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_data')->table('page_speed_audits', function (Blueprint $table) {
            $table->string('report_filename')->after('score')->nullable();
            $table->json('meta_data')->after('report_filename')->nullable();
            $table->json('data_raw')->nullable()->change();
            $table->json('data_normalized')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_data')->table('page_speed_audits', function (Blueprint $table) {
            $table->dropColumn(['report_filename','meta_data']);
        });
    }
};
