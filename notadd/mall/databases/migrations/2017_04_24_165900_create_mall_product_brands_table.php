<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-05-09 18:16:54
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallProductBrandsTable.
 */
class CreateMallProductBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_product_brands', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->comment('分类 ID');
            $table->string('logo')->nullable()->comment('品牌 Logo');
            $table->string('name')->comment('品牌名称');
            $table->string('initial')->comment('首字母');
            $table->tinyInteger('order')->default(0)->comment('排列顺序');
            $table->tinyInteger('recommend')->default(0)->comment('是否推荐');
            $table->enum('show', ['image', 'text'])->default('text')->comment('显示方式');
            $table->integer('store_id')->default(0)->comment('店铺 ID');
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
        $this->schema->drop('mall_product_brands');
    }
}
