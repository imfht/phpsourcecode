<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigManiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_manies', function (Blueprint $table) {
            $table->id();
            $table->char('group', 8); // 配置组索引
            $table->string('group_name'); // 配置组名称
            $table->char('config', 8); // 配置索引
            $table->string('config_name'); // 配置名称
            $table->text('config_value'); // 配置值
            $table->tinyInteger('status')->default(1);
            $table->string('description'); // 额外的描述
            $table->timestamps();
            $table->unique(['group', 'config']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_manies');
    }
}
