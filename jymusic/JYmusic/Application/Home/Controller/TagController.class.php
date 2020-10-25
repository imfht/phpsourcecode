<?php

namespace Home\Controller;
/**
 * 前台标签控制器
 */
class TagController extends HomeController {

    public function index(){
        $title= '音乐标签';
        $this->title = $title;
    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    	$title = "全部标签";
    	$this->assign('list',get_tag_tree());
    	$this->title = $title;
        $this->display();         
    }
            
    public function detail($id = 0){
    	/* 标识正确性检测 */
		$id = $id ? $id : I('get.show', 0);
		/* 获取分类信息 */
		$tag = D('Tag')->info($id);
		if($tag){
			//查找标签下歌曲
			$map['_string']  = 'FIND_IN_SET('.$tag['id'].',tags)' ;
			$list=$this->lists('Songs',$map); 
			$this->assign('tag', $tag);
    		$this->assign('list', $list);
    		$title =  !empty($tag['title'])? $tag['title'] : $tag['name'] ;
    		$this->meat_keywords = !empty($tag['keywords'])? $tag['keywords'] : C('WEB_SITE_KEYWORD');
       		$this->meat_description = !empty($tag['description'])? $tag['description'] : C('WEB_SITE_DESCRIPTION');	
    		$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
    		$this->title = $title;
    		$this->assign('data',$tag);
    		$this->display();
		} else {
			$this->error('标签不存在或被禁用！');
		}       
    }
       
}