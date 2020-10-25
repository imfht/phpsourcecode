<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-05-09 14:01:48
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallProductSpecificationsTable.
 */
class CreateMallProductSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_product_specifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->comment('分类 ID');
            $table->string('name')->comment('规格显示名称');
            $table->integer('store_id')->default(0)->comment('商家 ID');
            $table->enum('type', ['color', 'size', 'extend'])->default('color')->comment('规格类型');
            $table->string('value')->nullable()->comment('规格值');
            $table->string('order')->default(0)->comment('排序');
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
        $this->schema->drop('mall_product_specifications');
    }
}
