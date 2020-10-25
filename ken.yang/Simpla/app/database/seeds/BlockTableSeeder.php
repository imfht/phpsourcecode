<?php

/*
 * 区块表数据
 */

class BlockTableSeeder extends Seeder {

    public function run() {
        $data = array(
            array(
                'baid' => '4',
                'machine_name' => 'system_new_posts',
                'title' => '最新文章',
                'description' => '最新发布的文章',
                'body' => '',
                'type' => 'system',
                'callback' => 'Blocksystem::new_posts();',
                'format' => '',
                'theme' => '',
                'status' => '1',
                'weight' => '0',
                'pages' => '',
                'cache' => '0',
            ),
            array(
                'baid' => '4',
                'machine_name' => 'system_new_users',
                'title' => '新进用户',
                'description' => '最新注册的用户',
                'body' => '',
                'type' => 'system',
                'callback' => 'Blocksystem::new_users();',
                'format' => '',
                'theme' => '',
                'status' => '1',
                'weight' => '0',
                'pages' => '',
                'cache' => '0',
            ),
            array(
                'baid' => '4',
                'machine_name' => 'system_friend_link',
                'title' => '友情连接',
                'description' => '友情连接',
                'body' => '',
                'type' => 'system',
                'callback' => 'Blocksystem::friend_link();',
                'format' => '',
                'theme' => '',
                'status' => '1',
                'weight' => '0',
                'pages' => '',
                'cache' => '0',
            ),
        );
        foreach ($data as $item) {
            Block::create($item);
        }
    }

}
