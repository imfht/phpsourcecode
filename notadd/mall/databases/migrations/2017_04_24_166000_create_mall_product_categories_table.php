<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-04-24 17:11:52
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallCategoriesTable.
 */
class CreateMallProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_product_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('deposit')->default(0)->comment('保证金数额');
            $table->string('name')->comment('分类名称');
            $table->integer('parent_id')->default(0)->comment('父级分类 ID');
            $table->string('logo')->nullable()->comment('分类图片');
            $table->tinyInteger('order')->default(0)->comment('排序');
            $table->enum('show', ['spu', 'sku'])->default('spu')->comment('显示方式');
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
        $this->schema->drop('mall_product_categories');
    }
}
