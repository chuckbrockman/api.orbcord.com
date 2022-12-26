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
        Schema::connection('mysql_data')->create('cfcontests_form_data', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->index();
            $table->json('headers')->nullable();
            $table->json('form_data');
            $table->dateTime('created_at')->useCurrent()->index();
            $table->dateTime('updated_at')->nullable()->default(\DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_data')->dropIfExists('cfcontests_form_data');
    }
};
