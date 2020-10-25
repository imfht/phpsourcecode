<?php
// +-------------------------------------------------------------+
// | Copyright (c) 2014-2015 JYmusic音乐管理系统                 |
// +-------------------------------------------------------------
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Home\Controller;
/**
 * 前台专辑数据处理
 */
class AlbumController extends HomeController {
    //获取专辑数据
    public function index(){
    	$title= '全部专辑';
		//数据中查询记录
		$type=M('AlbumType')->field('id,name')->select();
		$list=$this->lists('Album');
		$this->assign('type', $type);
    	$this->assign('list', $list);   	
    	$this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
		$this->display();
    }
    //获取指定类型专辑数据
    public function type(){
    	$id = intval(I('id'));
    	if ($id){
	    	$Albumtype =  M('AlbumType'); 
			//数据中查询指定的ID记录
			$type=$Albumtype->field('id,name')->select();
			if(empty($type)) $this->error('你访问的专辑类型不存在');	
			//数据中查询指定的type ID的所有专辑记录
			$map['type_id'] =  $id;
	    	$list=$this->lists('Album',$map);
	    	$data = $Albumtype->where(array('id='.$id))->field('name,description')->find();	    	
	    	$this->title = $data['name'];
	    	$this->meat_title = $data['name'].' - '.C('WEB_SITE_TITLE');
	    	$this->assign('type', $type);
	    	$this->assign('list', $list);
			$this->display('index');
		}else {
		 	$this->error('页面出错');
		}
    }

    
    public function detail(){
    	$id = intval(I('id'));
    	$Album =  M('Album'); 
    	//数据中查询指定的ID记录
    	$map['id'] =  $id;
		$info=$Album->where($map)->find();
		if(empty($info)) $this->error('页面出错');	
		$Album->where($map)->setInc('hits'); // 点击数加1
		//数据中查询指定的专辑 ID的所有歌曲记录
    	$ma['album_id'] =  $id;
    	$list=$this->lists('Songs',$ma);  	
    	$title =  !empty($info['title'])? $info['title'] : $info['name'] ;
		$this->meat_keywords = !empty($info['keywords'])? $info['keywords'] : C('WEB_SITE_KEYWORD');
       	$this->meat_description = !empty($info['description'])? $info['description'] : C('WEB_SITE_DESCRIPTION');	
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$this->title = $title;
    	$this->assign('data', $info);
    	$this->assign('list', $list);
		$this->display();
    }
}