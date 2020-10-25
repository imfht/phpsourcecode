<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-06-29 14:35:12
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallProductPicturesTable.
 */
class CreateMallProductPicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_product_pictures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->comment('商品 ID');
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
        $this->schema->drop('mall_product_pictures');
    }
}
