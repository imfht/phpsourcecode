<?php
namespace Post\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function single($id=1){
        if(is_numeric($id)) {
        	mc_set_views($id);
        	if(mc_option('paixu')!=2) {
        		if(mc_get_page_field($id,'date')<=strtotime("now")) {
	        		mc_update_page($id,strtotime("now"),'date');
        		}
        	};
        	$this->page = M('page')->field('id,title,content,type,date')->where("id='$id'")->select();
			$this->theme(mc_option('theme'))->display('Post/single');
		} else {
			$this->error('参数错误！');
		}
    }
    public function checkout($id,$price){
    	if(mc_user_id()) {
	    	if(is_numeric($price)) {
		    	$this->theme(mc_option('theme'))->display('Post/checkout');
	    	} else {
		    	$this->error('价格必须为数字！');
	    	}
    	} else {
	    	$this->success('请先登陆！',U('User/login/index'));
    	}
    }
}