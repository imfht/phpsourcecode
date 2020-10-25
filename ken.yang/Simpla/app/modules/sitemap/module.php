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
function sitemap_access() {
    $list = array();

    $list['sitemap'] = array(
        'title' => '站点地图',
        'siderbar' => 'setting',
        'class' => 'fa-comment',
        'list' => array(
            array('as' => 'sitemap', 'title' => '站点地图', 'weight' => 10, 'description' => '站点地图配置'),
        )
    );

    return $list;
}
