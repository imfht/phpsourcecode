<?php
namespace App\Controller;

use Kernel\Loader;

use App\Model\Article as ArticleModel;
use App\Model\Category as CategoryModel;

class Index extends Controller
{

    public function index($p=1){
        $this->assign('TITLE','博客首页');

        $articles=Loader::singleton(ArticleModel::class)->getListPage('',6,$p,'/index-{page}.html');
        $this->assign(compact('articles'));

        return $this->fetch('index');
    }

}