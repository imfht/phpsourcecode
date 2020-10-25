<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Admin extends Migrator
{
    public function change()
    {
        $table = $this->table('admin');
        $table->addColumn('username', 'string', ['limit'=>50, 'default'=>'', 'comment'=>'登陆账号']);
        $table->addColumn('nickname', 'string', ['limit'=>50, 'default'=>'', 'comment'=>'昵称']);
        $table->addColumn('mobile', 'string', ['limit'=>11, 'default'=>'', 'comment'=>'手机号']);
        $table->addColumn('password', 'string', ['limit'=>255, 'default'=>'']);
        $table->addColumn('avatar', 'string', ['limit'=>255, 'default'=>'', 'comment'=>'头像']);
        $table->addColumn('register_ip', 'string', ['limit'=>20, 'default'=>'', 'comment'=>'IP']);
        $table->addColumn('login_time', 'integer', ['default'=>0, 'comment'=>'最后登陆时间']);
        $table->addColumn('login_ip', 'string', ['default'=>20, 'comment'=>'最后登陆IP']);
        $table->addColumn('login_num', 'integer', ['limit'=>4, 'default'=>0, 'comment'=>'登陆次数']);
        $table->addColumn('status', 'boolean', ['default'=>1, 'comment'=>'用户状态（0：禁用，1：开启）']);
        $table->addColumn('register_time', 'integer', ['default'=>0]);
        $table->addColumn('update_time', 'integer', ['default'=>0]);

        $table->insert([
            'username'=>'root',
            'nickname'=>'root',
            'mobile'=>'13000000000',
            'password'=>md5('123456'),
            'avatar'=>'/uploads/product/2018-11-08/5be3de5c38d63.png',
            'register_ip'=>'127.0.0.1',
            'login_ip'=>'0.0.0.0',
            'register_time'=>$_SERVER['REQUEST_TIME']
        ]);

        $table->save();

    }
}
