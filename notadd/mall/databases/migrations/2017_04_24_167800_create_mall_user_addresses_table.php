<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-06-29 17:22:38
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallUserAddressesTable.
 */
class CreateMallUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_user_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户 ID');
            $table->string('name')->comment('收货人姓名');
            $table->string('phone')->comment('电话号码');
            $table->string('location')->comment('所在区域');
            $table->string('address')->comment('详细地址');
            $table->tinyInteger('is_default')->default(0)->comment('默认地址');
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
        $this->schema->drop('mall_user_addresses');
    }
}
