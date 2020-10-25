<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Home\Controller;
use Think\Controller;
/**
 * 前台艺术家数据处理
 */
class ArtistController extends HomeController {
    //获取艺术家数据
    public function index(){
    	$title = '全部艺术家';
    	$Artisttype =  M('ArtistType');
    	$type=$Artisttype->field('id,name')->select();
		$list=$this->lists('Artist');
		$this->assign('type', $type);
    	$this->assign('list', $list);
    	$this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
		$this->display();
    }
    
    //获取艺术家分类数据
    public function type(){    	
    	$id = I('id');
    	$Artisttype =  M('ArtistType');
    	$type=$Artisttype->field('id,name')->select();
    	if(empty($type)) $this->error('页面出错');	
		//数据中查询指定的type ID的所有专辑记录
		$map['type_id']=$id;
    	$list=$this->lists('Artist',$map); 
    	$title = $type['name'];   	
    	$this->assign('type', $type);
    	$this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$this->assign('list', $list);
		$this->display('index');
    }
    
    public function detail($id=null,$type=null){
    	$id = intval(I('id'));
    	$type = I('type'); 	
    	$Artist =  M('Artist'); 
    	//数据中查询指定的ID记录
		$info=$Artist->where('id='.$id)->find();
		if(empty($info)) $this->error('页面出错');		
		//数据中查询指定的艺术家 ID的所有歌曲记录
		$map['artist_id']=$id;
		$info['songs']=M('Songs')->where($map)->count();//获取歌曲总数
		$info['albums']=M('Album')->where($map)->count();//获取专辑总数
    	if(isset($type) && $type == 'album' ){
    		$list=$this->lists('Album',$map);
    	}else{
    		$list=$this->lists('Songs',$map);
    	}
    	$Artist->where('id='.$id)->setInc('hits'); // 点击数加1
    	$title =  !empty($info['title'])? $info['title'] : $info['name'] ;
		$this->meat_keywords = !empty($info['keywords'])? $info['keywords'] : C('WEB_SITE_KEYWORD');
       	$this->meat_description = !empty($info['description'])? $info['description'] : C('WEB_SITE_DESCRIPTION');	
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$this->title = $title;
    	$this->assign('data', $info);
    	$this->assign('count', $count);
    	$this->assign('list', $list);
    	$this->assign('type', $type);
		$this->display();
    }
}