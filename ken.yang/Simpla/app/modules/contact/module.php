<?php

/*
 * 模块主文件，必须存在
 * ----------------------------------------------------------
 */

/**
 * hook_access
 * machine_name机器名字
 * title区块标题
 * description区块描述
 * callback返回函数---如果是当前文件中，直接写function，如果在model，则使用model中的调用方法
 */
function contact_access() {
    $list = array();

    $list['contact'] = array(
        'title' => '联系我们',
        'siderbar' => 'contact',
        'class' => 'fa-comment',
        'list' => array(
            array('as' => 'contact', 'title' => '联系我们', 'description' => ''),
            array('as' => 'admin_contact', 'title' => '联系列表', 'weight' => 1, 'description' => '查看和操作联系列表'),
        )
    );

    return $list;
}
