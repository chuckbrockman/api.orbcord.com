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
        Schema::connection('mysql_data')->create('webhook_data', function (Blueprint $table) {
            $table->id();
            $table->string('referrer');
            $table->string('source', 50)->index();
            $table->json('body');
            $table->dateTime('processed_at')->nullable()->index();
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
        Schema::connection('mysql_data')->dropIfExists('webhook_data');
    }
};
