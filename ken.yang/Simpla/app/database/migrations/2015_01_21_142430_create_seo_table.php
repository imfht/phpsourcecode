<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //创建表
        Schema::create('seo', function($table) {
            $table->increments('id', 11);
            $table->string('type', 64);
            $table->integer('cid')->references('id')->on('category');
            $table->integer('nid')->references('id')->on('note');
            $table->string('title', 256);
            $table->string('description', 256);
            $table->string('keywords', 256);
            $table->timestamps();
        });
        //添加数据
        DB::table('seo')->insert(array(
            'id' => '1',
            'type' => 'home',
            'cid' => '',
            'nid' => '',
            'title' => '首页',
            'description' => 'Simpla，一个开源免费的cms',
            'keywords' => 'Simpla,laravel,cms,开源cms，免费cms',
        ));
        DB::table('seo')->insert(array(
            'id' => '2',
            'type' => 'category',
            'cid' => '1',
            'nid' => '',
            'title' => '未分类',
            'description' => '未分类内容栏目',
            'keywords' => '',
        ));
        DB::table('seo')->insert(array(
            'id' => '3',
            'type' => 'node',
            'cid' => '',
            'nid' => '1',
            'title' => '关于Simpla',
            'description' => '关于Simpla',
            'keywords' => '',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('seo');
    }

}
