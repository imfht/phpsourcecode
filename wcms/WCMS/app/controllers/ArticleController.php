<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 11:03
 */
class ArticleController extends Action{

    public function getArticleByCid(){
            $artSer=new ArticleService();
            $rs=$artSer->getNewsByCid($_GET['cid'],$_GET['p']);
            $this->sendNotice("SUCCESS",$rs,'true');
    }

    public function getArticleByPage(){
        $artSer=new ArticleService();
        $rs=$artSer->getNewsByPage($_GET['p']);
        $this->sendNotice("SUCCESS",$rs,'true');
    }

    public function getArticleById(){
        $artSer=new ArticleService();
        $rs=$artSer->getNewsById($_GET['id']);
        $this->sendNotice("SUCCESS",$rs,'true');
    }
    
}