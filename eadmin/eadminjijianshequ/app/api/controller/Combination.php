<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\api\logic\Article as LogicArticle;

/**
 * 聚合接口控制器
 */
class Combination extends ApiBase
{

    /**
     * 首页接口
     */
    public function index()
    {

        list($article_category_list) = LogicArticle::getArticleCategoryList();
        list($article_list) = LogicArticle::getArticleList($this->param);

        return $this->apiReturn([compact('article_category_list', 'article_list')]);
    }

    /**
     * 详情接口
     */
    public function details()
    {

        list($article_category_list) = LogicArticle::getArticleCategoryList();
        list($article_details) = LogicArticle::getArticleInfo($this->param);

        return $this->apiReturn([compact('article_category_list', 'article_details')]);
    }
}
