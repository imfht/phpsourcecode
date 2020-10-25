<?php


namespace Ajax\Controller;
use Think\Controller;
use Think\Storage;

class IndexController extends Controller{
	
	public function index(){
                loadTPL(); //载入静态缓存
                $cid = I('get.cid');
                $cinfo = array();
                if(empty($cid)){
                    $cinfo = M('category')->field('id,title')->find();
                }else{
                    $cinfo = M('category')->where('id='.$cid)->field('id,title')->find();
                }
		$contentlist = M('content')->where('status=1 and time < '.time().' and c_id = '.$cinfo['id'])->field('id,title')->select();
                $categorylist = M('category')->where('id !='.$cinfo['id'])->field('id,title')->select();
                $this->assign('current_name', $cinfo['title']);
		$this->assign('contentlist', $contentlist);
                $this->assign('categorylist', $categorylist);
                $this->display();
	}
        
        //ajax获取内容
        public function ajaxgetcontent(){
            if(IS_AJAX){
                $id = I('post.id');
                $content = M('content')->where('status=1 and time < '.time().' and id = '.$id)->getField('content');
                echo $content;
                
            }
            
        }

	
}
