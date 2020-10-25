<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-04-25 16:20:33
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallProductDetailsTable.
 */
class CreateMallProductDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_product_details', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content_pc')->nullable();
            $table->text('content_mobile')->nullable();
            $table->integer('product_id');
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
        $this->schema->drop('mall_product_details');
    }
}
