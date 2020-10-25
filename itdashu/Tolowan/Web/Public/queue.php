<?php
$settings = array(
    'ceshi' => array(
        'name' => '测试',
        'description' => '重建私募经理、私募公司、私募产品的搜索索引，一般需要极大的系统资源，无需执行',
        'main' => 0,//是否加入主队列，主队列会每天定期执行
        'callFun' => 'Modules\Queue\Library\Common::ceshi',
        'time' => 0,
    ),
);
