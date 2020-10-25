<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayNotifyRawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_notify_raws', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type')->default('undefined')->comment('订单类型:一般写表名称:users');
            $table->text('raw');
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
        Schema::dropIfExists('pay_notify_raws');
    }
}
