<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('setting', function($table) {
            //$table->increments('id', 11);
            $table->string('name', 64);
            $table->text('value')->nullable();
            $table->INTEGER('status')->unsigned()->default(1);
            $table->text('extend')->nullable();
        });
        //添加数据
        DB::table('setting')->insert(array(
            'name' => 'admin_theme',
            'value' => 'default',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'theme_default',
            'value' => 'default',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_name',
            'value' => 'Simpla',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_mail',
            'value' => '',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_description',
            'value' => 'Simpla，一个基于Laravel的内容管理系统',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_url',
            'value' => '',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_logo',
            'value' => 'logo.png',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_copyright',
            'value' => 'Prowed By Simpla',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_tongji',
            'value' => '',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'user_is_allow_login',
            'value' => '1',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'user_is_allow_register',
            'value' => '1',
            'status' => '0',
            'extend' => '',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_cache',
            'value' => '0',
            'status' => '0',
            'extend' => '0',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_comment',
            'value' => '0',
            'status' => '0',
            'extend' => '0',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_version',
            'value' => '0.1',
            'status' => '0',
            'extend' => '0',
        ));
        DB::table('setting')->insert(array(
            'name' => 'site_maintenance',
            'value' => '0',
            'status' => '0',
            'extend' => '0',
        ));
        DB::table('setting')->insert(array(
            'name' => 'home_list_num',
            'value' => '10',
            'status' => '0',
            'extend' => '0',
        ));
        DB::table('setting')->insert(array(
            'name' => 'category_list_num',
            'value' => '10',
            'status' => '0',
            'extend' => '0',
        ));
        DB::table('setting')->insert(array(
            'name' => 'module_status',
            'value' => null,
            'status' => '0',
            'extend' => '0',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('setting');
    }

}
