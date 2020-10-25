<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-07-06 11:06:55
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallUserCartsTable.
 */
class CreateMallUserCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_user_carts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->comment('商品 ID');
            $table->integer('store_id')->comment('店铺 ID');
            $table->integer('user_id')->comment('用户 ID');
            $table->integer('price')->comment('加入时的商品价格');
            $table->tinyInteger('count')->default(0)->comment('商品数量');
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
        $this->schema->drop('mall_user_carts');
    }
}
