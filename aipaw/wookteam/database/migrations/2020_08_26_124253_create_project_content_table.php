<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_content', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('projectid')->nullable()->default(0)->comment('项目ID');
            $table->integer('taskid')->nullable()->default(0)->unique('IDEX_taskid')->comment('任务ID');
            $table->text('content')->nullable()->comment('内容');
            $table->bigInteger('indate')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_content');
    }
}
