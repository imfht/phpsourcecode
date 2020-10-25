<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Home\Controller;

/**
 * 前台曲风数据处理
 */
class GenreController extends HomeController {
    //获取音乐数据
    public function index($order=null){
    	$Genre = S('genre');
		if (empty($Genre)){
			$Genre = M('Genre')->field(true)->select();
			S('genre',$Genre);
		}	
		$order = I('order');
		$map = null;
		if(!empty($order)){
			$this->assign('order',$order);
			switch ($order){
				case 'new':$order = 'add_time desc';break;  
				case 'hot':$order = 'listens desc';break;
				case 'down':$order = 'download desc';break;
				case 'fav':$order = 'favtimes desc';break;
				case 'like':$order = 'likes desc';break;
				case 'rater':$order = 'rater desc';break;
				case 'rec':$map['recommend'] = '1'; $order = 'add_time desc';break; 
				default:$order = 'add_time desc';
			}
		}else{
			$this->assign('order','all');
		}
		$list=$this->lists('Songs',$map,$order);
		$title = '全部音乐';
		$this->meat_title = $title.' - '.C('WEB_SITE_NAME');
    	$this->title = $title;
    	$this->assign('list',$list);
    	$this->assign('genres',$Genre);
    	$this->assign('genreId',0);	
		$this->display();
    }
    
    public function detail($id=null,$order=null){
    	$Genre = S('genre');
		if (empty($Genre)){
			$Genre = M('Genre')->field(true)->select();
			S('genre',$Genre);
		}
		$id = intval(I('id'));
		$order = I('order');
		if(!empty($order)){
			$this->assign('order',$order);
			$map = null;
			switch ($order){
				case 'new':$order = 'add_time desc';break;  
				case 'hot':$order = 'listens desc';break;
				case 'down':$order = 'download desc';break;
				case 'fav':$order = 'favtimes desc';break;
				case 'like':$order = 'likes desc';break;
				case 'rater':$order = 'rater desc';break;
				case 'rec':$map['recommend'] = '1'; $order = 'add_time desc';break; 
				default:$order = 'add_time desc';
			}
		}else{
			$this->assign('order','all');
		}	
 		if (!empty($id)){//判断id
 			$this->assign('genreId',$id);
			$map['genre_id']=$id;
			$childId = M('Genre')->where(array('pid'=>$id))->field('id')->select();
			if(is_array($childId)){
				foreach ($childId as &$v) {
				 	 $id = $id.','.$v['id'];
				}
				$map['genre_id']= array('in',$id);
			}
			$list=$this->lists('Songs',$map,$order);
			$info = M('Genre')->where(array('id'=>$id))->field('cover_id,name,keywords,description')->find();
			$this->assign('info',$info);
			$title =  isset($info['title'])? $info['title'] : $info['name'] ;
			$this->meat_keywords = !empty($info['keywords'])? $info['keywords'] : C('WEB_SITE_KEYWORD');
       		$this->meat_description = !empty($info['description'])? $info['description'] : C('WEB_SITE_DESCRIPTION');
    		$this->title = $title;
    		$this->meat_title = $title.' - '.C('WEB_SITE_NAME');
    		$this->assign('list',$list);
    		$this->assign('genres',$Genre);		
    		$this->display();
    	}else{
    		
    		$this->error('页面不存在','index');
    	}
    
    }
        
    public function type(){
		//数据中查询指定的ID记录
		$id = intval(I('id')); 
		$Genre =  M('Genre'); 
		$map['genre_id']=$id;
		$list=$this->lists('Songs',$map);
		if(empty($list)) $this->error('页面出错');	
		$Genre = $Genre->getField('id,name');	
		$title =  $Genre[$id];	
		$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$this->title = $title;
    	$this->assign('list',$list);
    	$this->assign('genre',$Genre);   	
		$this->display('index');
    }
  
}