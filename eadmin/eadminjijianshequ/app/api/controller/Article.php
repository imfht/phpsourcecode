<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\api\logic\Article as LogicArticle;

/**
 * 文章接口控制器
 */
class Article extends ApiBase
{

    /**
     * 文章分类接口
     */
    public function categoryList()
    {

        return $this->apiReturn(LogicArticle::getArticleCategoryList());
    }

    /**
     * 文章列表接口
     */
    public function articleList()
    {

        return $this->apiReturn(LogicArticle::getArticleList($this->param));
    }
}
