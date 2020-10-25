<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-07-05 14:09:34
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallUserFollowsTable.
 */
class CreateMallUserCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_user_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户 ID');
            $table->integer('store_id')->comment('店铺 ID');
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
        $this->schema->drop('mall_user_collections');
    }
}
