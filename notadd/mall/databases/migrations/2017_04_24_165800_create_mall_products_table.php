<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-04-24 17:45:06
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallProductsTable.
 */
class CreateMallProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('library_id')->default(0)->comment('商品库 ID');
            $table->integer('store_id')->default(0)->comment('店铺 ID');
            $table->string('barcode')->nullable()->comment('商品条形码');
            $table->integer('brand_id')->default(0)->comment('品牌 ID');
            $table->integer('business_item')->nullable()->comment('商家货号');
            $table->integer('category_id')->default(0)->comment('分类 ID');
            $table->text('description')->nullable()->comment('商品描述');
            $table->string('name')->comment('商品名称');
            $table->decimal('price', 12, 2)->deault('0.00')->comment('价格');
            $table->string('price_cost')->deault('0.00')->comment('成本价格');
            $table->string('price_market')->deault('0.00')->comment('市场价格');
            $table->integer('inventory')->defualt(0)->comment('库存');
            $table->integer('inventory_warning')->defualt(0)->comment('库存预警值');
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
        $this->schema->drop('mall_products');
    }
}
