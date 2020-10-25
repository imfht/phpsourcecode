<?php
namespace Topic\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function single($id=1){
        if(is_numeric($id)) {
	        mc_set_views($id);
			$this->page = M('page')->field('id,title,content,type,date')->where("id='$id'")->select();
			$this->theme(mc_option('theme'))->display('Topic/single');
        } else {
	        $this->error('参数错误');
        }
    }
}