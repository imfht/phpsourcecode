<?php
//栏目分类
$assign_to_config['category_list']=array(
    '1'=>array(
        'name'=>'frontend',
        'title'=>'前端特效',
    ),
    '2'=>array(
        'name'=>'backend',
        'title'=>'后端处理',
    ),
    '3'=>array(
        'name'=>'service',
        'title'=>'服务环境',
    ),
    '4'=>array(
        'name'=>'tools',
        'title'=>'开发工具',
    ),
);

foreach ($assign_to_config['category_list'] as $k => $v) {
    $assign_to_config['category_names'][$v['name']]=$k;
}