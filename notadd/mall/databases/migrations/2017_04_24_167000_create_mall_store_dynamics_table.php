<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-05-09 13:55:59
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallStoreDynamicsTable.
 */
class CreateMallStoreDynamicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_store_dynamics', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content')->nullable()->comment('动态内容');
            $table->tinyInteger('show')->default(0)->comment('是否显示');
            $table->integer('store_id')->comment('店铺 ID');
            $table->string('thumbnail')->nullable()->comment('缩略图');
            $table->string('title')->comment('动态标题');
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
        $this->schema->drop('mall_store_dynamics');
    }
}
