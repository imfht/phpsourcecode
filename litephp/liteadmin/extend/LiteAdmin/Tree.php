<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/4
 * Time: 16:42
 */

namespace LiteAdmin;

/**
 * 树形操作
 * Class Tree
 * @package LiteAdmin
 */
class Tree
{
    /**
     * 数组转树
     * @param $array
     * @param string $pk
     * @param string $pkey
     * @param string $skey
     * @return array
     */
    public static function array2tree($array,$pk='id',$pkey='pid',$skey='_child') {
        $list = [];
        $tree = [];
        foreach ($array as $item){
            $list[$item[$pk]] = $item;
        }
        foreach ($list as &$item){
            if (isset($list[$item[$pkey]])){
                $list[$item[$pkey]][$skey][] = &$item;
            }else{
                $tree[] = &$item;
            }
        }
        return $tree;
    }

    /**
     * 树转列表
     * @param $tree
     * @param string $skey
     * @return array
     */
    public static function tree2list($tree,$skey='_child',$prefix='_pre',$level=0){
        $array = [];
        foreach ($tree as $item){
            $item[$prefix] = str_repeat('　｜',$level).($level?'—':'');
            if (isset($item[$skey])){
                $child = self::tree2list($item[$skey],$skey,$prefix,$level+1);
                unset($item[$skey]);
                $array[] = $item;
                $array = array_merge($array, $child);
            }else{
                $array[] = $item;
            }
        }
        return $array;
    }

    /**
     * 数组转列表
     * @param $array
     * @param string $pk
     * @param string $pkey
     * @param string $skey
     * @return array
     */
    public static function array2list($array,$pk='id',$pkey='pid',$skey='child'){
        $tree = self::array2tree($array, $pk, $pkey, $skey);
        return self::tree2list($tree, $skey);
    }

}