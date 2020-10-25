<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectMemberTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
		     * 此数据表用于记录项目的成员
		     */

        Schema::create('project_member', function(Blueprint $table){
            $table->increments('id');	//主键id

            $table->integer('project_id')->unsigned(); //项目id，外键
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->integer('member_id')->unsigned();	//成员id，外键
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('role_id')->unsigned();	//成员在该项目中的角色id，外键
            $table->foreign('role_id')->references('id')->on('projectRoles')->onDelete('cascade');
            $table->timestamps();	//成员的加入或角色更改时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_member');
    }
}
