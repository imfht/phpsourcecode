<?php
namespace User\Controller;
use Think\Controller;
class RefferController extends Controller {
    public function index($id,$page=1){
    	if(is_numeric($id)) {
	    	if(mc_user_id()==$id) {
		    	$args_id = M('meta')->where("meta_key='ref' AND meta_value='$id'")->getField('page_id',true);
	        	$condition['id']  = array('in',$args_id);
				$this->page = M('page')->where($condition)->order('id desc')->page($page,mc_option('page_size'))->select();
		    	$count = M('page')->where($condition)->count();
		        $this->assign('id',$id);
		        $this->assign('count',$count);
		        $this->assign('page_now',$page);
		    	$this->theme(mc_option('theme'))->display('User/reffer');
		    } else {
			    $this->error('您无权查看其他人的推荐记录！');
		    }
		} else {
	     	$this->error('参数错误！');
	    };	}
}