<?php

class LinkAction extends GlobalAction {

	public function index(){
		
		import('ORG.Util.Page');
		
		$link = D('link');
		
		$count = $link -> count();
		
		$Page = new Page($count, 20);
		
		$show = $Page -> show();

		$list = $link -> limit($Page -> firstRow, $Page -> listRows) -> select();
		
		$this -> assign('list', $list);
		
		$this -> assign('pageLink', $show);
		
		$this -> display();
	
	}
	
	public function add(){
	
		$this -> display();
	
	}
	
	public function addlink(){
	
		$data = I('post.');
	
		if(empty($data['name'])){
	
			$this -> error('链接名称不能为空');
	
		}
	
		if(empty($data['url'])) $this -> error('链接地址不能为空');
	
		if(empty($data['description'])) $this -> error('描述不能为空');
		
		foreach($data as $key => $val){
		
			$data[$key] = stopDefaultTag($val);
		
		}		
	
		$link = D('link');
	
		$result = $link -> add($data);
	
		if(empty($result)){
	
			$this -> error('添加失败');
	
		}else $this -> success('添加成功', U('Link/index'));
	
	}
	
	public function edit(){
	
		$id = intval($_GET['id']);
	
		$data = D('link') -> where('id='. $id) -> find();
	
		$this -> assign('data', $data);
	
		$this -> display();
	
	}
	
	public function editlink(){
	
		$data = I('post.');
		
		if(empty($data['id'])){
		
			$this -> error('链接不存在');
		
		}
	
		if(empty($data['name'])){
	
			$this -> error('链接名称不能为空');
	
		}
	
		if(empty($data['url'])) $this -> error('链接地址不能为空');
	
		if(empty($data['description'])) $this -> error('描述不能为空');
		
		foreach($data as $key => $val){
		
			$data[$key] = stopDefaultTag($val);
		
		}		
	
		$link = D('link');
	
		$result = $link -> save($data);
	
		if(empty($result)){
	
			$this -> error('编辑失败');
	
		}else $this -> success('编辑成功', U('Link/index'));
	
	}
	
	public function del(){
	
		$id = $_GET['id'];
	
		if(is_array($id)) $id = implode(',', $id);
	
		$result = 1;
	
		if(preg_match('/\d(\,\d)*/', $id)){
	
			$link = D('link');
	
			$result = $link -> delete($id);
	
			$result = $result > 0 ? 1 : 0;
	
		}else $result = 0;
	
		if($result) $this -> success('删除成功', U('Link/index'));
	
		else $this -> error('删除失败');
	
	}
	
}