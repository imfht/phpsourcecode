<?php
namespace Pro\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index($page=1){
        if(!is_numeric($page)) {
	        $this->error('参数错误');
        }
        if($_GET['keyword']) {
	        $where['content']  = array('like', "%{$_GET['keyword']}%");
			$where['title']  = array('like',"%{$_GET['keyword']}%");
			$where['_logic'] = 'or';
			$condition['_complex'] = $where;
			$condition['type']  = 'pro';
	        $this->page = M('page')->where($condition)->order('date desc')->page($page,mc_option('page_size'))->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->display('Pro/index');
        } else {
	        $condition['type'] = 'pro';
	        $this->page = M('page')->where($condition)->order('date desc')->page($page,mc_option('page_size'))->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->theme(mc_option('theme'))->display('Pro/index');
	    };
    }
    public function term($id,$page=1){
    	if(is_numeric($id) && is_numeric($page)) {
	    	//检索子分类
        	$args_id_t = M('meta')->where("meta_key='parent' AND meta_value='$id' AND type='term'")->getField('page_id',true);
        	if($args_id_t) {
				$condition_t['id']  = array('in',$args_id_t);
				$condition_t['type']  = 'term_pro';
				$terms_pro_t = M('page')->where($condition_t)->getField('id',true);
			};
			if($terms_pro_t) {
				//如果有子分类，获取子分类下商品
				$condition_child['meta_key'] = 'term';
				$condition_child['meta_value'] = array('in',$terms_pro_t);
				$condition_child['type'] = 'basic';
				$args_id_child = M('meta')->where($condition_child)->getField('page_id',true);
				//获取当前分类下商品
				$args_id_this = M('meta')->where("meta_key='term' AND meta_value='$id' AND type='basic'")->getField('page_id',true);
				if($args_id_child && $args_id_this) {
					$args_id = array_merge($args_id_child,$args_id_this);
				} elseif($args_id_this) {
					$args_id = $args_id_this;
				} elseif($args_id_child) {
					$args_id = $args_id_child;
				}
			} else {
				//如果没有子分类，直接获取当前分类下商品
				$args_id = M('meta')->where("meta_key='term' AND meta_value='$id' AND type='basic'")->getField('page_id',true);
			};
			if($args_id) {
				$condition['id']  = array('in',$args_id);
				$condition['type'] = 'pro';
				$this->page = M('page')->where($condition)->order('date desc')->page($page,mc_option('page_size'))->select();
		    	$count = M('page')->where($condition)->count();
		    };
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
			$this->theme(mc_option('theme'))->display('Pro/term');
		} else {
			$this->error('参数错误！');
		}
	}
    public function single($id=1){
        if(is_numeric($id)) {
        	mc_set_views($id);
			$this->page = M('page')->field('id,title,content,type,date')->where("id='$id'")->select();
			$this->theme(mc_option('theme'))->display('Pro/single');
        } else {
	        $this->error('参数错误');
        }
    }
}