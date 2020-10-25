<?php

class GlobalAction extends Action {

	function _initialize(){
		
		$session_id = $_REQUEST['s'];
		
		if($session_id && $session_id != session_id()){
		
			session_destroy();
		
			session_id($session_id);
		
			@session_start();
		
		}		
	
		if(!$_SESSION['hd_user']) $this -> redirect('Pub/login');
	
	}
	
	protected function isSuper(){
	
		return $_SESSION['hd_user']['isSuper'] ? true : false;
	
	}
	
	protected function listToTree($list, $pk = 'id', $pid = 'cid', $child = 'children', $root = 0){
	
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

	//创建目录
	protected function mkdirs($dir){
	
		if(!is_dir($dir)){
	
			if(!$this -> mkdirs(dirname($dir))){
	
				return false;
	
			}
	
			if(!mkdir($dir, 0777)){
	
				return false;
	
			}
	
		}
	
		return true;
	
	}	
	
}