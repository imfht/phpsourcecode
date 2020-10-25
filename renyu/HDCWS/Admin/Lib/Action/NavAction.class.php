<?php

class NavAction extends GlobalAction{
	
	public function getListTree(){
		
		$tid = $_GET['tid'] == 2 ? 2 : 1;
		
		$nav = D('nav');
		
		//$rootId = intval($_GET['id']);
		
		//if(empty($rootId)) $rootId = 0;
		
		//$list = $cat -> where('cid = ' . $rootId) -> select();
		
		$rootId = 0;
		
		$list = $nav -> where('tid=' . $tid) -> order('sort desc') -> select();
		
		$listTree = $this -> listToTree($list, 'id', 'nid', 'children', $rootId);
		
		$listTree = $this -> prepaire($listTree);
		
		echo json_encode(array('children' => $listTree, 'success' => true));
		
	}
	
	public function add(){
	
		$flag = true;
	
		$data = I('post.');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
	
		$nav = D('nav');
	
		$result = $nav -> add($data);
	
		if(empty($result)) $flag = false;
	
		echo json_encode(array('success' => $flag, 'id' => $result));
	
	}	
	
	public function edit(){

		$flag = true;
	
		$data = I('post.');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
	
		$nav = D('nav');
	
		$result = $nav -> save($data);
	
		if(empty($result)) $flag = false;
	
		echo json_encode(array('success' => $flag));

	}
	
	public function del(){
	
		$flag = true;
	
		$id = intval($_POST['id']);
	
		$nav = D('nav');
	
		$subs = $nav -> where(array('nid' => $id)) -> count();
	
		if(empty($id) || $subs) $flag = false;
	
		else{
				
			$result = $nav -> delete($id);
				
			if(empty($result)) $flag = false;
				
		}
	
		echo json_encode(array('success' => $flag));
	
	}	
	
}