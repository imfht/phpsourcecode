<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-05-09 11:45:06
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallOrderInvoicesTable.
 */
class CreateMallOrderInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_order_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content')->nullable();
            $table->integer('order_id');
            $table->string('title');
            $table->enum('type', ['normal', 'vat'])->default('normal');
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
        $this->schema->drop('mall_order_invoices');
    }
}
