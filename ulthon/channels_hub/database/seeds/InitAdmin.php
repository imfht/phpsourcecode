<?php

use app\model\Admin;
use think\helper\Str;
use think\migration\Seeder;

class InitAdmin extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $model_admin = Admin::where('account','admin')->find();

        if(empty($model_admin)){
            $salt = Str::random(6);
            $model_admin = Admin::create([
                'account'=>'admin',
                'password'=>md5('123456'.$salt),
                'salt'=>$salt
            ]);
        }
    }
}