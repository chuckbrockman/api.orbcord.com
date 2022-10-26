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
        Schema::connection('mysql_data')->create('page_speed_audit_webhook_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_speed_audit_id')->index();
            $table->unsignedBigInteger('webhook_data_id')->index();

            $table->foreign('page_speed_audit_id')->references('id')->on('page_speed_audits')->onDelete('NO ACTION');
            $table->foreign('webhook_data_id')->references('id')->on('webhook_data')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_data')->dropIfExists('page_speed_audit_webhook_data');
    }
};
