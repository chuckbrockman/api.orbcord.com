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
        Schema::connection('mysql_data')->create('page_speed_audit_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('code', 50)->index();
            $table->dateTime('created_at')->useCurrent()->index();
            $table->dateTime('updated_at')->nullable()->default(\DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'));
            $table->dateTime('deleted_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_data')->dropIfExists('page_speed_audit_sources');
    }
};
