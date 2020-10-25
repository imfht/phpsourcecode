<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-07-12 11:09:01
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallProductLibrariesTable.
 */
class CreateMallProductLibrariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_product_libraries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_id')->default(0)->comment('品牌 ID');
            $table->integer('category_id')->default(0)->comment('分类 ID');
            $table->string('name')->comment('商品名称');
            $table->string('selling_point')->nullable()->comment('商品卖点');
            $table->string('barcode')->nullable()->comment('商品条形码');
            $table->string('image')->nullable()->comment('商品图片');
            $table->string('price_range')->nullable()->comment('价格区间');
            $table->string('public_praise')->nullable()->comment('口碑');
            $table->string('delivery_area')->nullable()->comment('配送区域');
            $table->string('production_place')->nullable()->comment('商品产地');
            $table->text('description')->nullable()->comment('商品描述');
            $table->text('description_mobile')->nullable()->comment('商品描述');
            $table->string('weight')->nullable()->comment('商品重量');
            $table->string('size')->nullable()->comment('商品体积');
            $table->string('flow_marketing')->nullable();
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
        $this->schema->drop('mall_product_libraries');
    }
}
