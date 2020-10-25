<?php
namespace app\article\service;
/**
 * 站点导航接口
 */
class NavService {
    /**
     * 获取导航结构
     */
    public function getSiteNav() {

        $list = target('article/ArticleClass')->loadTreeList();
        return array(
            'article' => array(
                'name' => '文章',
                'target' => 'article/articleClass',
                'list' => $list,
            ),
        );
    }
}

