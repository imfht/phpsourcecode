<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 通知类型表 notifyTypes,包含
 *     id:(表主键)
 *     name:(通知类型,如:project)
 *     label:(通知类型标签,如通知类型为project,则对应类型标签为:项目通知)
 *     map:(通知类型对应的模型映射,如project映射的模型为Project)
 * 四个字段.
 */

class CreateNotifyTypesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifyTypes', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name', 20);
            $table->string('label', 26);
            $table->string('map', 30);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notifyTypes');
    }
}

