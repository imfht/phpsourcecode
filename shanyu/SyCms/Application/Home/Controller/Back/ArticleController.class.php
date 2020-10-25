<?php
namespace Home\Controller;
use Think\Controller;
class ArticleController extends Controller {
    //文章内页
    public function index() {

        $category=M('Category')->field('id,pid,title,mid')->where('status=1')->select();
        $category=\Lib\ArrayTree::listTree($category);
        $this->assign('category',$category);

        
        $id=I('get.id',0);
        $info=M('Article')->find($id);
        $this->assign('info',$info);
        $this->display();
    }
    	
}
