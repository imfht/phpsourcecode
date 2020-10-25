<?php

/*
 * User表测试数据
 */

class UserTableSeeder extends Seeder {

    public function run() {
        //DB::table('users')->delete();
        $data = array(
            array(
                'username' => 'admin',
                'password' => Hash::make('123456'),
                'email' => '695093513@qq.com',
                'picture' => 'upload/author/default.png'
            ),
            array(
                'username' => 'testuser1',
                'password' => Hash::make('123456'),
                'email' => 'teseuser1@test.com',
                'picture' => 'upload/author/default.png'
            ),
            array(
                'username' => 'testuser2',
                'password' => Hash::make('123456'),
                'email' => 'teseuser2@test.com',
                'picture' => 'upload/author/default.png'
            )
        );
        foreach ($data as $item) {
            User::create($item);
        }
    }

}
