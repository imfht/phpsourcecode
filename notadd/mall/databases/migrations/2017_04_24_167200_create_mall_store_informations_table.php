<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-06-28 11:58:31
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallStoreInformationsTable.
 */
class CreateMallStoreInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_store_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->comment('店铺 ID');
            $table->string('company')->comment('公司名称');
            $table->string('location')->comment('公司所在地');
            $table->string('address')->comment('公司详细地址');
            $table->string('telephone')->comment('公司电话');
            $table->string('employees')->comment('员工总数');
            $table->string('capital')->comment('注册资金');
            $table->string('contacts')->comment('联系电话');
            $table->string('email')->comment('电子邮箱');
            $table->string('licence_number')->comment('营业执照号');
            $table->string('licence_location')->comment('营业执照所在地');
            $table->string('licence_validity')->nullable()->comment('营业执照有效期');
            $table->string('licence_sphere')->comment('法定经营范围');
            $table->string('licence_image')->nullable()->comment('营业执照电子版');
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
        $this->schema->drop('mall_store_informations');
    }
}
