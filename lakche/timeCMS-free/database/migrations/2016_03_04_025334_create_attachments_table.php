<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->comment('附件地址');
            $table->string('name')->comment('附件名称');
            $table->string('thumb')->comment('微缩图地址');
            $table->integer('sort')->comment('排序');
            $table->integer('is_recommend')->comment('是否推荐');
            $table->integer('is_show')->comment('是否显示');
            $table->integer('is_cover')->comment('是否封面');
            $table->string('type')->comment('附件类型');
            $table->string('attr')->comment('对应模型');
            $table->string('hash')->comment('模型hash');
            $table->unsignedInteger('project_id')->comment('对应模型ID');

            $table->softDeletes();
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
        Schema::dropIfExists('attachments');
    }
}
