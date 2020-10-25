<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Home\Controller;
use Think\Page;
/**
 * 前台排行数据处理,排行功能待完善
 */
class RanksController extends HomeController {
    //默认页面 试听排行
    public function index(){
    	$title = '最新推荐';	
		//数据中查询指定的ID记录
		$order='recommend';		
		$list=$this->ranksLists($order);
    	$this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$this->assign('index',0);//仅仅只是为了css样式
    	$this->assign('list',$list);
		$this->display();   	
    }
    
    public function listens () {
        $title = '试听排行';	
		//数据中查询指定的ID记录
		$list=$this->ranksLists('listens desc');		
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$this->title = $title;
    	$this->assign('list',$list);
    	$this->assign('index',1);//仅仅只是为了css样式
		$this->display('index');
    }
  
  	//评分
    public function rater(){
    	$title = '评分排行';	
		//数据中查询指定的ID记录
		$list=$this->ranksLists('rater desc');		
    	$this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$this->assign('list',$list);
    	$this->assign('index',2);//仅仅只是为了css样式
		$this->display('index');
    }
    
    
    public function down(){
    	$title = '下载排行';	
		//数据中查询指定的ID记录	
		$list=$this->ranksLists('download desc');
    	$this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$this->assign('list',$list);
    	$this->assign('index',3);//仅仅只是为了css样式
		$this->display('index');
    }
    public function fav(){
    	$title = '收藏排行';	
		//数据中查询指定的ID记录
		$order='favtimes desc';		
		$list=$this->ranksLists($order);
    	$this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$this->assign('list',$list);
    	$this->assign('index',4);//仅仅只是为了css样式
		$this->display('index');
    }
    
    public function latest () {
        $title = '最新上传';	
		//数据中查询指定的ID记录	
		$list=$this->ranksLists('add_time desc');
    	$this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$this->assign('index',5);//仅仅只是为了css样式
    	$this->assign('list',$list);
		$this->display('index');
    
    }
       
    public function ranksLists( $order, $map=array() ,$field=true ){
		$model = M('Songs');
		$field = !is_null($field)? $field :'id,name,add_time,recommend';
		$map['status'] =1; 
		if ('recommend' == $order) {$map['recommend'] = 1; $order = 'id desc' ;}
		$count =  $model->where($map)->count();//获取总数
		$ranksTotal=C('RANKS_SONGS_TOTAL');
       	$songsList=C('RANKS_SONGS_LIST_ROWS');
		$total = isset($ranksTotal) ? $ranksTotal : 100;
		$total =  $total>$count ?  $count:$total ;
		$listRows = isset($songsList) ? $songsList : 20;
		$page = new \Think\Page($total, $listRows);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page->setConfig('prev', '上一页');
        	$page->setConfig('next', '下一页');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $limit = $page->firstRow.','.$page->listRows;
        $time= intval(C('RANKS_SONGS_CACHE_TIME'));
        $cacheTime = $time? $time : 24*60*60; 
		return $model->cache(true,$cacheTime)->field($field)->where($map)->order($order)->limit($limit)->select();
	}
}