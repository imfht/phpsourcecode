<?php

namespace app\controller\admin;

use app\controller\BaseController;
use app\model\Article as ArticleModel;
use app\model\Auth as AuthModel;
use Input;
use View;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * 首页控制器
 * @author 徐亚坤 hdyakun@sina.com
 */
class IndexController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $page = Input::get('p');
        $articles = ArticleModel::lists();
        $auths = AuthModel::lists();
        View::make('index')
            ->with('article', $articles[0])
            ->with('title', $articles[0]->title . 'index')
            ->with('auths', $auths)
            ->with('page', $page)
            ->show();
        echo microtime(true) - START_TIME;
        echo "<br>";
        echo memory_get_usage() - START_USAGE_MEMORY;
        exit();
    }


    public function testAction()
    {
        echo "Test.";
    }
}