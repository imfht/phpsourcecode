<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-04-25 16:23:00
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallOrdersTable.
 */
class CreateMallOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('address_id')->comment('用户地址 ID');
            $table->integer('user_id')->comment('用户 ID');
            $table->integer('store_id')->comment('店铺 ID');
            $table->enum('status', ['order', 'pay', 'express'])->default('order')->comment('订单状态');
            $table->string('flow_marketing')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->drop('mall_orders');
    }
}
