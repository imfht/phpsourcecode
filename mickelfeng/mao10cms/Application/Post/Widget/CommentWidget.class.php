<?php
namespace Post\Widget;
use Think\Controller;
class CommentWidget extends Controller {
    public function index($id){
        if(is_numeric($id)) {
    		if($_GET['comment']=='all') {
	    		$this->comment = M('action')->where("page_id='$id' AND action_key='comment'")->order('id desc')->select();
    		} else {
	    		$this->comment = M('action')->where("page_id='$id' AND action_key='comment'")->order('id desc')->page(1,10)->select();
    		}
	        $this->assign('page_id',$id);
	        $this->theme(mc_option('theme'))->display("Public:comment");
        }
    }
}