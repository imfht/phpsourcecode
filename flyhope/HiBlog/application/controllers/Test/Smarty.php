<?php
use Model\Article;
/**
 * Smarty测试
 *
 * @author chengxuan <i@chengxuan.li>
 */
class Test_SmartyController extends Yaf_Controller_Abstract {

    public function indexAction() {
        
        
        Model\Publish::sidebar();
        exit;
        
//         $result = Model\Theme\Resource::showByName(2, 'article');
//         var_dump($result);exit;
        
        $article = Model\Article::show(8);
        Model\Publish::article($article);

        
        $smarty = Comm\Smarty::init();
        $smarty->display('tpl:article', ['var' => '<u>b</u>']);
        return false;
    }
    
}
