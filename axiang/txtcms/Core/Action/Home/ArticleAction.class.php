<?php
/**
 * TXTCMS 文章模块
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class ArticleAction extends HomeAction {
	public $Arctype;
	public $Article;
	public function _init(){
		parent::_init();
		$this->Arctype=DB('arctype');
		$this->Article=DB('article');
	}
	public function Index(){
		$this->display();
	}
	public function lists(){
		$data=array();
		$id=isset($_GET['id'])?intval($_GET['id']):'0';
		$this->tplConf('cache_lifetime',config("cache_lifetime_channel")*3600);
		if(config("cache_lifetime_channel")==0) $this->tplConf('caching',false);
		$cache_id=md5($id.$this->_get('p'));
		$this->tplConf('cache_id',$cache_id);
		$data=$this->Arctype->where('id='.$id)->find();
		if($data){
			if($this->Arctype->where('pid='.$id)->find()) $data['sonclass']=true;
			//获取上级分类信息
			$result=$this->Arctype->where('id='.$data['pid'])->find();
			if($result){
				$data['purl']=get_list_url($result['id']);
				$data['pname']=$result['cname'];
			}
			if($id=='0') $data['cname']='所有文章';
			$data['cid']=$id;
			$this->assign($data);
		}else{
			_404('栏目不存在');
		}
		$this->display();
	}
	public function show(){
		$data=array();
		$id=isset($_GET['id'])?intval($_GET['id']):$this->error('id不能为空');
		$this->tplConf('cache_lifetime',config("cache_lifetime_view")*3600);
		if(config("cache_lifetime_view")==0) $this->tplConf('caching',false);
		$cache_id=md5($id.$this->_get('p'));
		$this->tplConf('cache_id',$cache_id);
		$data=$this->Article->where('id='.$id)->find();
		if(!$data) _404('文章不存在');
		$class=$this->Arctype->where('id='.$data['cid'])->find();
		$data['cname']=$class['cname'];
		$data['thisurl']=get_show_url($id);
		$data['curl']=get_list_url($data['cid']);
		//获取上级分类信息
		$result=$this->Arctype->where('id='.$class['pid'])->find();
		if($result){
			$data['purl']=get_list_url($result['id']);
			$data['pname']=$result['cname'];
		}
		if($data['flag']<>''){
			$flags=explode(',',$data['flag']);
			$flag=array();
			foreach($flags as $kk=>$vv){
				$flagresult=DB('arcflag')->where('en='.$vv)->find();
				$flag[]=array('cn'=>$flagresult['cn'],'en'=>$vv);
			}
			$data['flag']=$flag;
		}
		$result=DB('arcbody')->getHash($data['id'],true)->where('id='.$data['id'])->find();
		if($result) $data['body']=$result['body'];
		$this->assign($data);
		$this->display();
	}
	public function search(){
		$data=array();
		$this->tplConf('caching',false);
		$data['title']=isset($_REQUEST['q'])?htmlspecialchars($_REQUEST['q']):$this->error('关键词不能为空');
		$cache_id=md5($_SERVER['QUERY_STRING']);
		$this->tplConf('cache_id',$cache_id);
		$this->assign($data);
		$this->display();
	}
}