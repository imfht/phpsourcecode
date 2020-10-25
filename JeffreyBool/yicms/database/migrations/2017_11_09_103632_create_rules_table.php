<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index()->comment('权限名称');
            $table->string('route',255)->nullable()->comment('权限路由');
            $table->integer('parent_id')->default(0)->unsigned()->index()->comment('上级权限');
            $table->tinyInteger('is_hidden')->default(0)->unsigned()->index()->comment('是否隐藏');
            $table->integer('sort')->default(255)->unsigned()->comment('排序');
            $table->tinyInteger('status')->index()->default(1)->comment('状态: 1 正常, 2=>禁止');
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
        Schema::dropIfExists('rules');
    }
}
