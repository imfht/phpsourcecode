<?php

/*
 * User表测试数据
 */

class NodeTableSeeder extends Seeder {

    public function run() {
        $data = array(
            array(
                'type' => 'page',
                'uid' => '1',
                'title' => '欢迎使用Simpla',
                'status' => '1',
                'comment' => '0',
                'view' => '0',
                'promote' => '1',
                'sticky' => '1',
                'plusfine' => '0',
            ),
        );
        foreach ($data as $item) {
            User::create($item);
        }
    }

}
