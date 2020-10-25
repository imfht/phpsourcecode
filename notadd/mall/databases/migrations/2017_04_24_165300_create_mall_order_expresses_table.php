<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-05-09 12:29:45
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallOrderExpressesTable.
 */
class CreateMallOrderExpressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_order_expresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('address_id');
            $table->integer('courier_number');
            $table->string('express_company');
            $table->integer('order_id');
            $table->integer('user_id');
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
        $this->schema->drop('mall_order_expresses');
    }
}
