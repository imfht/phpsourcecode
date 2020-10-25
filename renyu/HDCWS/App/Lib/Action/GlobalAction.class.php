<?php

class GlobalAction extends Action {
	
	private $navType = 1;//1为顶部，2底部

	function _initialize(){
		
		StatisticsAction::addRecord();
		
		$headNav = $this -> setUrl($this -> getHeadNavList());
		
		$footNav = $this -> setUrl($this -> getFootNavList());
		
		$this -> assign('headNavList', $headNav);
		
		$this -> assign('footNavList', $footNav);
	
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

	protected function getHeadNavList(){
	
		$nav = D('nav');

		$navList = $nav -> field('id,nid,name,url') -> order('sort desc') -> where('tid = 1') -> select();
		
		$tree = $this -> listToTree($navList, 'id', 'nid', 'list');
		
		return $tree;
	
	}
	
	protected function getFootNavList(){
	
		$nav = D('nav');

		$navList = $nav -> field('id,nid,name,url') -> order('sort desc') -> where('tid = 2') -> select();
		
		$tree = $this -> listToTree($navList, 'id', 'nid', 'list');
		
		return $tree;
	
	}	
	
	protected function listToTree($list, $pk = 'id', $pid = 'pid', $child = 'list', $root = 0){
	
	    // 创建Tree
	    $tree = array();
	
	    if(is_array($list)) {
	
	        // 创建基于主键的数组引用
	        $refer = array();
	
	        foreach ($list as $key => $data) {
	
	            $refer[$data[$pk]] =& $list[$key];
	
	        }
	
	        foreach ($list as $key => $data) {
	
	            // 判断是否存在parent
	            $parentId = $data[$pid];
	
	            if ($root == $parentId) {
	
	                $tree[] =& $list[$key];
	
	            }else{
	
	                if (isset($refer[$parentId])) {
	
	                    $parent =& $refer[$parentId];
	
	                    $parent[$child][] =& $list[$key];
	
	                }
	
	            }
	
	        }
	
	    }
	    
	    return $tree;
	
	}
	
	public function verify(){
		
		import('ORG.Util.Image');
		
		Image::buildImageVerify(4, 1, 'png', 50, 20);
		
	}	
	
}