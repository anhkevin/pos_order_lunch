<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_types', function (Blueprint $table) {
            $table->increments('id');
            $table->date('order_date');
            $table->string('order_name');
            $table->integer('shop_id')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->integer('pay_type')->unsigned();
            $table->integer('is_default')->unsigned()->default(0);
            $table->integer('assign_user_id')->unsigned()->default(0);
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
        Schema::dropIfExists('order_types');
    }
}
