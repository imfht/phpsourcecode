<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-07-20 14:14:47
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallOrderProductsTable.
 */
class CreateMallOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->comment('订单 ID');
            $table->integer('product_id')->comment('商品 ID');
            $table->decimal('price', 12, 2)->default(0.00)->comment('商品价格');
            $table->decimal('price_original', 12, 2)->default(0.00)->comment('商品原始价格');
            $table->integer('discount')->default(100)->comment('商品折扣');
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
        $this->schema->drop('mall_order_products');
    }
}
