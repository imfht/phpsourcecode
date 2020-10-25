<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 14-10-20
 * Time: 下午11:40
 */

class UserTableTestSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->delete();

        DB::table('users')->insert([
            'username'=>'admin',
            'password'=>Hash::make('admin'),
            'email'=>'admin@example.com', 'created_at'=>date('Y-m-d'),
            'head_image'=>'admin.png',
            'description'=>'我就是管理员',
            'updated_at'=>date('Y-m-d')
        ]);

        DB::table('users')->insert([
            'username'=>'spatra',
            'password'=>Hash::make('spatra'),
            'email'=>'spatra@sp.com', 'created_at'=>date('Y-m-d'),
            'description'=>'学渣苦',
            'head_image'=>'spatra.jpg',
            'updated_at'=>date('Y-m-d')
        ]);

        DB::table('users')->insert([
            'username'=>'spare',
            'password'=>Hash::make('spare'),
            'email'=>'spatra@sp.com', 'created_at'=>date('Y-m-d'),
            'description'=>'我是备胎，不是spatra',
            'head_image'=>'default.png',
            'updated_at'=>date('Y-m-d')
        ]);
    }

} 