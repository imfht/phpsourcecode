<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-06-28 14:04:44
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallStoreOutletsTable.
 */
class CreateMallStoreOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_store_outlets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->comment('店铺 ID');
            $table->string('name')->comment('门店名称');
            $table->string('address')->comment('详细地址');
            $table->string('telephone')->comment('联系电话');
            $table->string('bus_information')->comment('公交信息');
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
        $this->schema->drop('mall_store_outlets');
    }
}
