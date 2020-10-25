<?php

class NavAction extends GlobalAction{
	
	public function index(){
		
		import('ORG.Util.Page');
		
		$nav = D('nav');
		
		$rootId = 0;
		
		$list = $nav -> order('tid asc,sort desc') -> select();
		
		$listTree = $this -> listToTree($list, 'id', 'nid', 'children', $rootId);

		$this -> assign('list', $listTree);
		
		$count = $nav -> count();
		
		$Page = new Page($count, 2000);
		
		$show = $Page -> show();
		
		$this -> assign('pageLink', $show);		
	
		$this -> display();
	
	}
	
	public function add(){
		
		$id = intval($_GET['id']);
		
		if(!empty($id)){
			
			$nav = D('nav');

			$data = $nav -> where('id=' . $id) -> find();
			
			if(!empty($data)) $this -> assign('data', $data);
			
		}
		
		$this -> display();
	
	}
	
	public function adddata(){
		
		$data = I('post.');
		
		if(empty($data['name'])){
		
			$this -> error('导航名称不能为空');
			
		}
		
		if(empty($data['url'])) $this -> error('链接不能为空');
		
		if(!preg_match('/^\d+$/', $data['sort'])) $this -> error('排序须是正整数');
		
		foreach($data as $key => $val){
		
			$data[$key] = stopDefaultTag($val);
		
		}
		
		$nav = D('nav');
		
		$result = $nav -> add($data);
		
		if(empty($result)){
		
			$this -> error('添加失败');
				
		}else $this -> success('添加成功', U('Nav/index'));	
	
	}		
	
	public function edit(){

		$id = intval($_GET['id']);
		
		$nav = D('nav');
		
		$prefix = C('DB_PREFIX');

		$data = $nav-> query('select c.*,pc.name nname from ' . $prefix . 'nav c left join ' . $prefix . 'nav pc on c.nid = pc.id where c.id = ' . $id);

		if(!empty($data)){
			
			$this -> assign('data', $data[0]);
		
			$this -> display();
			
		}else $this -> error('导航不存在');

	}
	
	public function editdata(){
	
		$data = I('post.');
	
		if(empty($data['name'])){
	
			$this -> error('导航名称不能为空');
	
		}
	
		if(empty($data['url'])) $this -> error('链接不能为空');
		
		if(!preg_match('/^\d+$/', $data['sort'])) $this -> error('排序须是正整数');
		
		foreach($data as $key => $val){
		
			$data[$key] = stopDefaultTag($val);
		
		}
	
		$nav = D('nav');
	
		$result = $nav -> save($data);
	
		if(empty($result)){
	
			$this -> error('编辑失败');
	
		}else $this -> success('编辑成功', U('Nav/index'));
	
	}	
	
	public function del(){
	
		$flag = true;
		
		$id = intval($_GET['id']);
		
		$nav = D('nav');
		
		$subs = $nav -> where(array('nid' => $id)) -> count();

		if($subs) $flag = false;
		
		else{
		
			$result = $nav -> delete($id);
			
			if(empty($result)) $flag = false;
			
		}
	
		if($flag) $this -> success('删除成功', U('Nav/index'));
	
		else $this -> error('删除失败,该导航下有子导航');
	
	}	

	public function sysnav(){
		
		$staticNav = C('HD_Static_Nav');
		
		$this -> assign('staticNav', $staticNav);

		$this -> display();
		
	}
	
}