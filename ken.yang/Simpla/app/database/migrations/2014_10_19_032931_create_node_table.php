<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('node', function($table) {
            $table->increments('id', 11); // ID
            $table->string('type', 32); //内容类型
            $table->INTEGER('uid')->unsigned()->default(0); //用户ID
            $table->string('title', 256); //标题
            $table->longText('body'); //内容
            $table->enum('status', array('0', '1'))->default(1); //状态
            $table->INTEGER('comment')->unsigned()->default(0); //评论数量
            $table->INTEGER('view')->unsigned()->default(0); //查看数量
            $table->INTEGER('promote')->unsigned()->default(0); //是否推荐到首页
            $table->INTEGER('sticky')->unsigned()->default(0); //是否置顶
            $table->INTEGER('plusfine')->unsigned()->default(0); //是否加精华
            $table->timestamps();
        });
        //添加数据
        DB::table('node')->insert(array(
            'type' => 'page',
            'uid' => '1',
            'title' => '关于Simpla',
            'body' => '<p>Simpla,源自于世界语，意味简单的意思，同英语里的Simple。</p><p>一起从简单开始。</p>',
            'status' => '1',
            'comment' => '0',
            'view' => '0',
            'promote' => '0',
            'sticky' => '0',
            'plusfine' => '0',
            'created_at' => '2015-04-25 00:00:00',
            'updated_at' => '2015-04-25 00:00:00',
        ));
        DB::table('node')->insert(array(
            'type' => 'article',
            'uid' => '1',
            'title' => '欢迎使用Simpla',
            'body' => '<p>欢迎使用Simpla。这是系统自动生成的演示文章。编辑或者删除它，然后开始您的站点！</p><p>如有任何疑问或问题，请通过社区<a href="http://www.simplahub.com" target="_blank">www.simplahub.com</a>寻求帮助</p>',
            'status' => '1',
            'comment' => '0',
            'view' => '0',
            'promote' => '1',
            'sticky' => '1',
            'plusfine' => '0',
            'created_at' => '2015-04-25 00:00:00',
            'updated_at' => '2015-04-25 00:00:00',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('node');
    }

}
