<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-05-09 12:31:57
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallOrderRatesTable.
 */
class CreateMallProductRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_product_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->comment('订单 ID');
            $table->integer('product_id')->comment('商品 ID');
            $table->integer('user_id')->comment('用户 ID');
            $table->text('comment')->nullable()->comment('评论内容');
            $table->tinyInteger('rate')->default(0)->comment('星星评分');
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
        $this->schema->drop('mall_product_rates');
    }
}
