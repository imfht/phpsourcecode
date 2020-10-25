<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function _initialize(){
		$this->title="代码轮子-发现/分享/进步";
	}
    public function index(){
		$this->ids=$ids=I("get.");
		if($ids['tag_name']){
			$tag_name=urldecode(urldecode($ids['tag_name']));
			//$con="`tag` like '%".$tag_name."%'";
			$con['tag']=$tag_name;
		}else{
			$con=NULL;
		}
		if($ids['type']=="fav"){
			$con['fav']=1;
		}
		$lz_infos = M('lz_infos');
		$count      = $lz_infos->where($con)->count();
		$Page       = new \Think\Page($count,15);
		$Page->setConfig('theme','<ul class="pager"><li class="previous">%UP_PAGE%</li><li>%FIRST%</li><li>%LINK_PAGE%</li><li>%END%</li><li class="next">%DOWN_PAGE%</li></ul>');
		$show       = $Page->show();
		$list = $lz_infos->where($con)->order('datetime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('rs',$list);
		$this->assign('page',$show);
		$this->display();
    }
	
	public function tmp_search(){
		$p=I("post.");
		$out['url']=U('Index/search',array('keyword'=>urlencode($p['keyword'])));
		$this->ajaxReturn($out,'JSON');
	}
	
	public function search(){
		$this->ids=$ids=I("get.");
		if($ids['keyword']){
			$this->keyword=$keyword=urldecode(urldecode($ids['keyword']));
			$con="`tag` like '%".$keyword."%' or `title` like '%".$keyword."%'";
		}else{
			$con="";
		}
		$lz_infos = M('lz_infos');
		$count      = $lz_infos->where($con)->count();
		$Page       = new \Think\Page($count,15);
		$Page->setConfig('theme','<ul class="pager"><li class="previous">%UP_PAGE%</li><li>%FIRST%</li><li>%LINK_PAGE%</li><li>%END%</li><li class="next">%DOWN_PAGE%</li></ul>');
		$show       = $Page->show();
		$list = $lz_infos->where($con)->order('datetime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('rs',$list);
		$this->assign('page',$show);
		$this->display('index');
	}
	
	public function add_info(){
		$this->title="【发布】-代码轮子";
		$this->ids=$ids=I("get.");
		$con['bid']=$ids['bid'];
		$this->rs=M('lz_infos')->where($con)->find();
		$this->display();
	}
	
	public function post_add_info(){
		$this->ids=$ids=I("get.");
		$p=I("post.");
		$p['email']=$_SESSION['email'];
		$p['datetime']=date("Y-m-d H:i:s");
		$p['body']=htmlspecialchars($p['body']);
		unset($p['tag_list']);
		
		if($ids['bid']){
			unset($p['tag']);
			unset($p['tid']);
			unset($p['tag_color']);
			$con['bid']=$ids['bid'];
			M('lz_infos')->where($con)->save($p);
		}else{
			$bid=M('lz_infos')->add($p);
			set_tag_info($bid,$p['tid'],$p['title']);
		}

		
		header('Location: '.U('Index/index'));
	}
	
	public function view_info(){
		$ids=I("get.");
		$con['bid']=$ids['bid'];
		$this->rs=$rs=M('lz_infos')->where($con)->find();
		$r['hit']=$rs['hit']+1;
		M('lz_infos')->where($con)->save($r);
		$this->display();
	}
	
	public function goto_url(){
		$ids=I("get.");
		$con['bid']=$ids['bid'];
		$rs=M('lz_infos')->where($con)->find();
		$r['hit']=$rs['hit']+1;
		M('lz_infos')->where($con)->save($r);
		if($rs['url']){
			header('Location: '.$rs['url']);
		}else{
			header('Location: '.U('Index/view_info',array('bid'=>$rs['bid'])));
		}
		
	}
	
	public function tag(){
		$this->title="【分类】-代码轮子";
		$this->rs=M('lz_tags')->select();
		$this->display();
	}
	
	public function set_fav(){
		$p=I("post.");
		$con['bid']=$p['bid'];
		$r['fav']=1;
		M('lz_infos')->where($con)->save($r);
		$out['ok']=1;
		$this->ajaxReturn($out,'JSON');
	}
	
	public function del(){
		$p=I("post.");
		$con['bid']=$p['bid'];
		M('lz_infos')->where($con)->delete();
		$out['ok']=1;
		$this->ajaxReturn($out,'JSON');
	}
	
	public function edit(){
		$p=I("post.");
		$out['url']=U('Index/add_info',array('bid'=>$p['bid']));
		$this->ajaxReturn($out,'JSON');
	}
	
	public function about(){
		$this->display();
	}
}