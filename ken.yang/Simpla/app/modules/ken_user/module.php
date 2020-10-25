<?php

/*
 * 模块主文件，必须存在
 * ----------------------------------------------------------
 */

/**
 * hook_block_info
 * machine_name机器名字
 * title区块标题
 * description区块描述
 * callback返回函数---如果是当前文件中，直接写function，如果在model，则使用model中的调用方法
 * 实例：
 * $list[] = array(
 * 'machine_name' => 'test_block',
 * 'title' => '测试区块A',
 * 'description' => '测试区块描述A',
 * 'callback' => 'test_block',
 * );
 */
function ken_user_block_info() {
    $list = array();

    $list[] = array(
        'machine_name' => 'ken_new_user_list',
        'title' => '新用户列表',
        'description' => '以头像的形式展示用户列表',
        'callback' => 'Ken_user::new_user_list();',
    );

    return $list;
}
