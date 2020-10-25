<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-05-09 13:53:52
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallStoresTable.
 */
class CreateMallStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->default(0)->comment('所属分类');
            $table->integer('user_id')->default(0)->comment('店铺所有者');
            $table->string('name')->comment('店铺名称');
            $table->string('company')->nullable()->comment('公司名称');
            $table->string('location')->nullable()->comment('所在地区');
            $table->string('address')->nullable()->comment('店铺地址');
            $table->timestamp('open_at')->nullable()->comment('开店时间');
            $table->timestamp('end_at')->nullable()->comment('有效期至');
            $table->tinyInteger('grade')->default(0)->comment('店铺等级');
            $table->enum('status', ['review', 'opening', 'closed', 'banned'])->default('review')->comment('店铺状态');
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
        $this->schema->drop('mall_stores');
    }
}
