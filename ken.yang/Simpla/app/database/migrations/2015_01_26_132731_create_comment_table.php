<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //创建表
        Schema::create('comment', function($table) {
            $table->increments('id', 11);
            $table->string('title', 128);
            $table->string('machine_name', 256);
            $table->longtext('code_one');
            $table->longtext('code_two');
            $table->INTEGER('choose')->unsigned()->default(1);
        });
        //添加数据
        DB::table('comment')->insert(array(
            'id' => '1',
            'title' => '搜狐畅言',
            'machine_name' => 'changyan',
            'code_one' => '',
            'code_two' => '',
            'choose' => '1',
        ));
        DB::table('comment')->insert(array(
            'id' => '2',
            'title' => '多说',
            'machine_name' => 'duoshuo',
            'code_one' => '',
            'code_two' => '',
            'choose' => '1',
        ));
        DB::table('comment')->insert(array(
            'id' => '3',
            'title' => '国外disqus',
            'machine_name' => 'disqus',
            'code_one' => '',
            'code_two' => '',
            'choose' => '1',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('comment');
    }

}
