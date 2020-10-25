<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-06-29 11:37:07
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallStoreNavigationsTable.
 */
class CreateMallStoreNavigationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_store_navigations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('导航名称');
            $table->tinyInteger('is_show')->default(0)->comment('是否显示');
            $table->string('url')->nullable()->comment('链接地址');
            $table->integer('store_id')->comment('店铺 ID');
            $table->tinyInteger('order')->default(0)->comment('排序');
            $table->tinyInteger('parent_target')->default(0)->comment('新窗口打开');
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
        $this->schema->drop('mall_store_navigations');
    }
}
