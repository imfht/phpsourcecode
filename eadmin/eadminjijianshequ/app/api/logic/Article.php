<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\api\logic;

use app\common\logic\Article as LogicArticle;

/**
 * 文章接口逻辑
 */
class Article extends ApiBase
{

    public static $articleLogic = null;


    public static function initLogic()
    {

        static::$articleLogic = get_sington_object('articleLogic', LogicArticle::class);
    }

    /**
     * 获取文章分类列表
     */
    public static function getArticleCategoryList()
    {

        static::initLogic();

        $list = static::$articleLogic->getArticleCategoryList([], 'id,name', 'id desc', false);

        return [$list];
    }

    /**
     * 获取文章列表
     */
    public static function getArticleList($data = [])
    {

        static::initLogic();

        $where = [];

        !empty($data['category_id']) && $where['category_id'] = $data['category_id'];

        $list = static::$articleLogic->getArticleList($where, 'id,name,category_id,describe,create_time', 'create_time desc');

        return [$list];
    }

    /**
     * 获取文章信息
     */
    public static function getArticleInfo($data = [])
    {

        static::initLogic();

        $info = static::$articleLogic->getArticleInfo(['id' => $data['article_id']], 'id,name,category_id,describe,content,create_time');

        $info['content'] = html_entity_decode($info['content']);

        return [$info];
    }
}
