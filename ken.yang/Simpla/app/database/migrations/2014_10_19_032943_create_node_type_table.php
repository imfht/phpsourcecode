<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeTypeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('node_type', function($table) {
            $table->string('type', 32); //内容类型
            $table->string('name', 256);//内容类型名字
            $table->string('description', 256)->nullable();//内容类型描述
            
            $table->primary(array('type'));
        });
        //添加数据
        DB::table('node_type')->insert(array(
            'type' => 'page',
            'name' => '基础页面',
            'description' => '对您的静态内容使用基本页面，比如"关于我们"页面',
        ));
        //添加数据
        DB::table('node_type')->insert(array(
            'type' => 'article',
            'name' => '文章',
            'description' => '使用文章发布有关时间的内容，如消息，新闻或日志',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('node_type');
    }

}
