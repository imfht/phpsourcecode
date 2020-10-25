<?php
namespace App\Controller;

use Kernel\Loader;

use App\Model\Article as ArticleModel;
use App\Model\Category as CategoryModel;

class Page extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function about(){
        $this->assign('TITLE','关于博主');
        
        return $this->fetch('page_about');
    }

}