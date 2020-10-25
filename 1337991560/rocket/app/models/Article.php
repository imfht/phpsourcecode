<?php

namespace app\model;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use \MadphpDao\Blog\Article as ArticleDao;

/**
 * 模型类
 * @author 徐亚坤 hdyakun@sina.com
 */

class Article extends Base
{

    public function __construct()
    {

    }

    public static function lists()
    {
        return ArticleDao::Dao()->getAll();
    }

}