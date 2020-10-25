<?php
namespace Post\Controller;
use Think\Controller;
class GroupController extends Controller {
    public function index($page=1){
    	if(is_numeric($page)) {
	    	$this->page = M('page')->where('type="publish"')->order('date desc')->page($page,mc_option('page_size'))->select();
		    $count = M('page')->where('type="publish"')->count();
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
			$this->theme(mc_option('theme'))->display('Post/index');
		} else {
			$this->error('参数错误！');
		}
	}
	public function single($id,$page=1){
    	if(is_numeric($id) && is_numeric($page)) {
		   	$args_id = M('meta')->where("meta_key='group' AND meta_value='$id'")->getField('page_id',true);
	        if($args_id) :
	        	$condition['type'] = 'publish';
		        $condition['id']  = array('in',$args_id);
		        $this->page = M('page')->where($condition)->order('date desc')->page($page,mc_option('page_size'))->select();
		        $count = M('page')->where($condition)->count();
		    endif;
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
		    $this->theme(mc_option('theme'))->display('Post/group');
		} else {
	     	$this->error('参数错误！');
	    };
	}
}