<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronTabTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crontabs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('set_day');
            $table->time('set_hour');
            $table->string('set_group_laka');
            $table->text('set_content');
            $table->integer('disabled')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crontabs');
    }
}
