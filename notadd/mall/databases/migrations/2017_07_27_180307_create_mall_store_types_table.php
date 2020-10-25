<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-07-27 18:03:07
 */

use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallStoreTypesTable.
 */
class CreateMallStoreTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_store_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('类型名称');
            $table->decimal('amount_of_deposit', 12, 2)->default(0.00)->comment('保证金数额');
            $table->tinyInteger('order')->default(0)->comment('排序');
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
        $this->schema->drop('mall_store_types');
    }
}
