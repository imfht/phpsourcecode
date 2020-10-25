<?php
namespace Article\Controller;
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
			$condition['type']  = 'article';
	        $this->page = M('page')->where($condition)->order('id desc')->page($page,mc_option('page_size'))->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->display('Article/index');
        } else {
	        $condition['type'] = 'article';
	        $this->page = M('page')->where($condition)->order('id desc')->page($page,mc_option('page_size'))->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->theme(mc_option('theme'))->display('Article/index');
	    };
    }
    public function term($id,$page=1){
    	if(is_numeric($id) && is_numeric($page)) {
	    	$condition['type'] = 'article';
        	$args_id = M('meta')->where("meta_key='term' AND meta_value='$id' AND type='basic'")->getField('page_id',true);
        	$condition['id']  = array('in',$args_id);
	    	$this->page = M('page')->where($condition)->order('date desc')->page($page,mc_option('page_size'))->select();
		    $count = M('page')->where($condition)->count();
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
			$this->theme(mc_option('theme'))->display('Article/term');
		} else {
			$this->error('参数错误！');
		}
	}
    public function single($id=1){
        if(is_numeric($id)) {
	        mc_set_views($id);
			$this->page = M('page')->field('id,title,content,type,date')->where("id='$id'")->select();
			$this->theme(mc_option('theme'))->display('Article/single');
        } else {
	        $this->error('参数错误');
        }
    }
    public function tag($tag,$page=1){
    	if(is_numeric($page)) {
	    	$condition['type'] = 'article';
	    	$date = strtotime("now");
	    	$tag = addslashes($tag);
        	$args_id = M('meta')->where("meta_key='tag' AND meta_value='".mc_magic_in($tag)."' AND type='basic'")->getField('page_id',true);
        	$condition['id']  = array('in',$args_id);
	    	$this->page = M('page')->where($condition)->order('date desc')->page($page,mc_option('page_size'))->select();
		    $count = M('page')->where($condition)->count();
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
			$this->theme(mc_option('theme'))->display('Article/term');
		} else {
			$this->error('参数错误！');
		}
	}
}