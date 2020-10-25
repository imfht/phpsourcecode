<?php
/**
 * @author 七殇酒
 * @qq 739800600
 * @email 739800600@qq.com
 * @date 2013-07-09
 */
function tree($source)
{
    $result = [];
    //定义索引数组，用于记录节点在目标数组的位置，类似指针
    $points = [];

    foreach ($source as $region) {
        $points[$region['id']] = isset($points[$region['id']]) ? 
                array_merge($region, $points[$region['id']]) : $region;
            
        if ($region['parentId'] == 0) {
            $result[] = &$points[$region['id']];

        } else {
            if (!isset($points[$region['parentId']])) {
                 $points[$region['parentId']] = array();
            }
            if (!isset($points[$region['parentId']]['children'])) {
                $points[$region['parentId']]['children'] = array();
            }
            $points[$region['parentId']]['children'][] = &$points[$region['id']];
        }
    }
    return $result;
}
/*$a = [
    [
        'id' => 8,
        'name' => '八',
        'parentId' => 7
    ],
    [
        'id' => 1,
        'name' => '一',
        'parentId' => 0
    ],
    [
        'id' => 2,
        'name' => '二',
        'parentId' => 0
    ],
    [
        'id' => 3,
        'name' => '三',
        'parentId' => 1
    ],
    [
        'id' => 4,
        'name' => '四',
        'parentId'=> 1
    ],
    [
        'id' => 5,
        'name' => '五',
        'parentId' => 3
    ],
    [
        'id' =>6,
        'name' => '六',
        'parentId' =>3
    ],
    [
        'id' => 7,
        'name' => '七',
        'parentId' => 6
    ]
];

print_r(tree($a));*/