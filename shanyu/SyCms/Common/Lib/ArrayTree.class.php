<?php
namespace Lib;

//用于数组转换 无限级数组的类
class ArrayTree {

//一维数组转多维数组
static public function listTree($list, $root = 0,$pk='id', $pid = 'pid', $child = '_child') {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

//多维数组转一维数组
static public function treeList($tree, $child = '_child', $order='id', &$list = array()){
    if(is_array($tree)) {
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby='asc');
    }
    return $list;
}

//带标记的一维数组 array('│', '├', '└','─');
static public function listLevel($cate,$parentid = 0, $level = 0, $mark ='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') {
    $arr = array();
    foreach ($cate as $k => $v) {
        if ($v['pid'] == $parentid) {
            if($level>0){
                $v['mark'] = str_repeat($mark, $level);
            }else{
                $v['mark'] = '';
            }
            $v['level'] = $level + 1;
            $arr[] = $v;
            $return = self::listLevel($cate, $v['id'], $v['level'], $mark);
            // if(empty($return)){
            //     $_end=array_pop($arr);
            //     $_end['end']=$level;
            //     array_push($arr,$_end);
            // }
            $arr = array_merge($arr,$return);
        }
    }
    return $arr;
}

//城市联动专用
static public function listSelect($cate, $child = 'childid',$parent='parentid',$parentid = 0){
    $arr = array();
    foreach ($cate as $v) {
        if ($v[$parent] == $parentid) {
            $t['v'] = $v['id'];
            $t['n'] = $v['cityname'];
            $t['s'] = self::listSelect($cate, $child, $parent,$v['id']);
            $arr[] = $t;
        }
    }
    return $arr;
}

//获得父级元素的数组集合
static public function getParents($cate, $id) {
    $arr = array();
    foreach ($cate as $v) {
        if ($v['id'] == $id) {
            $arr[] = $v;
            $arr = array_merge(self::getParents($cate, $v['pid']), $arr);
        }
    }
    return $arr;
}

}