<?php
class ArticleadminController extends AdminController{


     public function getAllCon(){
         $ser=new ArticleService();
         $rs=$ser->getNewsByPage($_GET['p']);
         $this->view()->assign('num',$rs['page']);
         $this->view()->assign('rs',$rs['data']);
         $this->view()->display("file:article/article.html");
     }



     public function removeConById(){
         $ser=new ArticleService();
         $ser->removeNewsById($_POST['id']);
         $this->sendNotice("删除成功",null,true);
     }

     public function getConById(){
         $ser=new ArticleService();
         $cateSer=new CateService();

         $cate=$cateSer->getCategory();
         $this->view()->assign('cate',$cate);
         $rs=$ser->getNewsById($_GET['id']);
         $this->view()->assign('rs',$rs);
         $this->view()->display("file:article/edit.html");
     }

     public function addCon(){
         $cateSer=new CateService();
         $cate=$cateSer->getCategory();
         $this->view()->assign('cate',$cate);
         $this->view()->display('file:article/add.html');
     }


    public function upload(){
        $ser=new ArticleService();
        $ser->upload($_POST['type']);
    }



    public function saveCon(){
         $ser=new ArticleService();
         $ser->saveCon($_POST);
         $this->redirect("保存成功","/index.php?articleadmin/getallcon");
     }


     public function subCon(){
         $ser=new ArticleService();
         $ser->addCon($_POST);
         $this->redirect("提交成功","/index.php?articleadmin/getallcon");
     }
}