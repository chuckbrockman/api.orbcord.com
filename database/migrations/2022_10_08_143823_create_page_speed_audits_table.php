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
        Schema::connection('mysql_data')->create('page_speed_audits', function (Blueprint $table) {
            $table->id();
            // $table->unsignedInteger('page_speed_audit_source_id')->index();
            $table->string('audit_source', 25)->index();
            $table->string('audit_type', 25)->index();
            $table->string('url', 512)->index();
            $table->string('device_type', 25)->index();
            $table->decimal('score', 8, 4)->default(0);
            $table->json('data_raw');
            $table->json('data_normalized')->null();
            $table->unsignedInteger('ip_address');
            $table->json('referrer')->nullable();
            $table->dateTime('created_at')->useCurrent()->index();
            $table->dateTime('updated_at')->nullable()->default(\DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'));
            $table->dateTime('deleted_at')->nullable()->index();

            // $table->foreign('page_speed_audit_source_id')->references('id')->on('page_speed_audit_sources')->onDelete('NO ACTION');
            // $table->foreign('site_id')->references('id')->on('sites')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_data')->dropIfExists('page_speed_audits');
    }
};
