<?php

//用于数组转换 无限级数组的类
class Tree {

//一维数组转多维数组
static public function listToTree($list, $root = 0,$pk='id', $pid = 'pid', $child = '_child') {
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
static public function treeToList($tree, $child = '_child', $order='id', &$list = array()){
    if(is_array($tree)) {
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                self::treeToList($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = self::listSortBy($list, $order, $sortby='asc');
    }
    return $list;
}

//对查询结果集进行排序
static public function listSortBy($list,$field, $sortby='asc'){
    if(is_array($list)){
        $refer = $resultSet = array();
        foreach($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key=> $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 获得所有父级栏目
 * @param $data 栏目数据
 * @param $sid 子栏目
 * @param string $fieldPri 唯一键名，如果是表则是表的主键
 * @param string $fieldPid 父ID键名
 * @return array
 */
static public function getParent($data, $sid, $fieldPri = 'id', $fieldPid = 'pid')
{
    if (empty($data)) {
        return $data;
    } else {
        $arr = array();
        foreach ($data as $v) {
            if ($v[$fieldPri] == $sid) {
                $arr[] = $v;
                $_n = self::getParent($data, $v[$fieldPid], $fieldPri, $fieldPid);
                if (!empty($_n)) {
                    $arr = array_merge($arr, $_n);
                }
            }
        }
        return $arr;
    }
}



}