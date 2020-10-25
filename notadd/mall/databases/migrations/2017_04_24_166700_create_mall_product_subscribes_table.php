<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-06-23 15:24:39
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallProductSubscribesTable.
 */
class CreateMallProductSubscribesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_product_subscribes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->comment('商品 ID');
            $table->integer('store_id')->comment('店铺 ID');
            $table->string('status')->comment('订阅状态');
            $table->integer('user_id')->comment('用户 ID');
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
        $this->schema->drop('mall_product_subscribes');
    }
}
