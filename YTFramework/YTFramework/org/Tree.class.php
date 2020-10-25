<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Tree.class.php 89 2016-04-21 02:53:46Z lixiaomin $
 *  @created    2015-10-10
 *  分类树
 * =============================================================================                   
 */

namespace org;

use core\Config;

class Tree
{

    /**
     * 分类树 数组
     * @param type $list
     * @param type $parentid
     * @return type
     */
    function getTree($list, $parentid = 0)
    {

        $child = $this->getChild($list, $parentid);
        if (!empty($child)) {
            foreach ($child as $v) {
                $v['child'] = $this->getTree($list, $v['id']);
                $tree[] = $v;
            }
            return $tree;
        }
    }

    /**
     * 获取子节点
     * @param int $parentid
     * @return array
     */
    function getChild($tree_souce, $parentid = 0)
    {
        $c_tree = [];
        foreach ($tree_souce as $v) {
            if ($v['parentid'] == $parentid) {
                $c_tree[] = $v;
            }
        }
        return $c_tree;
    }

    /**
     * 树形列表
     * @staticvar array $subs
     * @param type $arr
     * @param type $id
     * @param type $lev
     * @return type
     */
    function getTreeList($arr, $id, $lev = 0)
    {
        static $subs = array();
        foreach ($arr as $v) {
            if ($v['parentid'] == $id) {
                $v['lev'] = $lev;
                $subs[] = $v;
                $this->getTreeList($arr, $v['id'], $lev + 1);
            }
        }
        return $subs;
    }

}

