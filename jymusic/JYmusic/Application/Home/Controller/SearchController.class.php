<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Home\Controller;
use Think\Controller;
class SearchController extends HomeController {
    public function index($keys,$type){	   	    		   	
    	$keys      =   addslashes(trim(I('get.keys')));  
    	$type      =   addslashes(trim(I('get.type')));
    	if (strstr($keys,'/')||strstr($type,'/') )$this->error('请填写关键词！'); 
    	if ($keys){
    		$type =  !empty($type)? $type:'songs';   		
    		$typename="";
	    	switch ($type) { 	    		
	    		case 'songs'	:
	    			$map['name'] = array('like',"%{$keys}%");
	    	 		$list = $this->lists('Songs',$map,null);
	    	 		$typename ="音乐";
	   			break;
	    		
	    		case 'album'	:
	    			$map['name'] = array('like',"%{$keys}%");
	    	 		$list = $this->lists('Album',$map);
	    	 		$typename = "专辑";
	   			break;
	   			
	   			case 'artist'	:
	   				$map['name'] = array('like',"%{$keys}%");
	    	 		$list = $this->lists('Artist',$map);
	    	 		$typename = "音乐人";
	   			break;
	   			
	   			case 'lrc'	:
	   				$map['lrc'] = array('like',"%{$keys}%");
	    	 		$list = $this->lists('Songs',$map,null,'lrc');
	    	 		$typename = "歌词";
	   			break;
	   			
	   			case 'user'	:
	   				$map['nickname'] = array('like',"%{$keys}%");
	    	 		$list = $this->lists('Member',$map,'uid DESC');
	    	 		$typename = "用户";
	   			break;
	    		
	    		default:	    	 	
	    	 	$list = '';
    		}   	
    		$this->assign('list', $list);
    		$this->assign('typename', $typename);
    		$this->meat_title = $typename.'搜索 - '.C('WEB_SITE_TITLE');
	    	$this->title = $typename.'搜索';
	    	$this->assign('type', $type);
	    	$this->assign('keys',$keys);
	    	$this->display($type);
    	}else{
    		$this->error('请填写关键词！');
    	}

    }
}