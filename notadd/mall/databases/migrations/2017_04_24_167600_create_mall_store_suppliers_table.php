<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-06-28 12:30:48
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallStoreSuppliersTable.
 */
class CreateMallStoreSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_store_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->comment('店铺 ID');
            $table->string('name')->comment('供货商名称');
            $table->string('contacts')->comment('联系人');
            $table->string('telephone')->comment('联系电话');
            $table->string('comments')->comment('备注信息');
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
        $this->schema->drop('mall_store_suppliers');
    }
}
