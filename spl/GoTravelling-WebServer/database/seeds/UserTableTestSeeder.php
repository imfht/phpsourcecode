<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-11
 * Time: 下午8:54
 */

use Illuminate\Database\Seeder;

class UserTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();

        DB::table('users')->insert([
            'username' => 'spatra',
            'cellphone_number' => '15625015373',
            'password' => Hash::make('spatra'),

            'email' => 'spatra@qq.com',
            'nickname' => 'spatra',
            'sex' => '男',
            'description' => '无可奉告',
            'address' => '地球，保密',
            'head_image' => 'spatra.jpg',
        ]);

        DB::table('users')->insert([
            'username' => 'zero',
            'cellphone_number' => '15625014560',
            'password' => Hash::make('zero'),

            'email' => 'zero@qq.com',
            'nickname' => 'zero',
            'sex' => '男',
            'description' => '无可奉告,oh yes',
            'address' => '地球,oh yes',
            'head_image' => 'spatra.jpg',
        ]);
        DB::table('users')->insert([
           'username' => 'test',
            'cellphone_number' => '13725217633',
            'password' => Hash::make('test123'),
            'email' => 'test@163.com',
            'nickname' => 'testNickName',
            'sex' => '男',
            'description' => '无可奉告,oh yes',
            'address' => '地球,oh yes',
            'head_image' => 'spatra.jpg'
        ]);


    }
}