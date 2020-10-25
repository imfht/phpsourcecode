<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-05-09 12:27:44
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallOrderProcessesTable.
 */
class CreateMallOrderProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_order_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->enum('status', ['payment', 'delivery', 'receive', 'done', 'canceled'])->default('payment');
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
        $this->schema->drop('mall_order_processes');
    }
}
