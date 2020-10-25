<?php

/**
 * 商城分类
 */

namespace app\mall\middle;

class CategoryMiddle extends \app\base\middle\BaseMiddle {

    /**
     * 树形分类
     */
    protected function treeList() {
        $list = target('mall/MallClass')->loadList();
        $treeList = target('mall/MallClass')->getTree($list);
        return $this->run([
            'treeList' => $treeList
        ]);
    }

    /**
     * 媒体信息
     */
    protected function meta() {
        $this->setMeta('商品分类');
        $this->setName('商品分类');
        $this->setCrumb([
            [
                'name' => '商品分类',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

}