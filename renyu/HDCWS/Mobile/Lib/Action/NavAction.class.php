<?php

class NavAction extends GlobalAction {

    public function index(){

		$nav = D('nav');
		
		$rootId = 0;
		
		$list = $nav -> field('id,nid,name,url') -> order('tid asc,sort desc') -> select();

		$list = $this -> setUrl($list);
		
		$listTree = $this -> listToTree($list, 'id', 'nid', 'children', $rootId);

		$this -> assign('list', $listTree);	
		
		$this -> display();

    }
	
	private function setUrl($list){
	
		foreach($list as $key => $val){
	
			if(!preg_match('/http\:\/\//', $val['url'])){
	
				$list[$key]['url'] = U($val['url']);
	
				$list[$key]['url'] = str_replace('amp;', '', $list[$key]['url']);
	
			}
				
			if(!empty($val['list'])) $list[$key]['list'] = $this -> setUrl($list[$key]['list']);
	
		}
	
		return $list;
	
	}
	
}