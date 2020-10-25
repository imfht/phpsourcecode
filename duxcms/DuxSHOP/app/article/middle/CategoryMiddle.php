<?php

/**
 * 商城分类
 */

namespace app\article\middle;

class CategoryMiddle extends \app\base\middle\BaseMiddle {

    /**
     * 树形分类
     */
    protected function treeList() {
        $list = target('article/ArticleClass')->loadList();
        $treeList = target('article/ArticleClass')->getTree($list);
        return $this->run([
            'treeList' => $treeList
        ]);
    }

    /**
     * 媒体信息
     */
    protected function meta() {
        $this->setMeta('文章分类');
        $this->setName('文章分类');
        $this->setCrumb([
            [
                'name' => '文章分类',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

}