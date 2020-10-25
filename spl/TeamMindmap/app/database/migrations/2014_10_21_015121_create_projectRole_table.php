<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectRoleTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 此数据库用于记录项目管理中成员的角色类型
         */

        Schema::create('projectRoles', function(Blueprint $table){
            $table->increments('id')->comment('主键id');

            $table->string('name')->comment('角色名称, 英文，用于内部使用');
            $table->string('label')->comment('角色标签，中文，用于外部显示');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('projectRoles');
    }
}
