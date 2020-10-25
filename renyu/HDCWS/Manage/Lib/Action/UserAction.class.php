<?php

class UserAction extends GlobalAction {
	
	function _initialize(){
	
		if(!($this -> isSuper())) exit;
	
	}

	public function index(){
		
		import('ORG.Util.Page');
		
		$user = D('user');
		
		$key = $_GET['key'];
		
		$condition = ' where (p.name like "%' . $key . '%" or p.email like "%' . $key . '%") and (p.name <> "admin") ';				

		$count = $user -> query('select count(*) counts from ' . C('DB_PREFIX') . 'user p ' . $condition);

		$count = empty($count) ? 0 : $count[0]['counts'];
		
		$Page = new Page($count, 20);
		
		$show = $Page -> show();
		
		$sql = 'select id,name,email,addtime,lastlogin,lastip,status from ' . C('DB_PREFIX') . 'user p ' . $condition . ' order by addtime desc limit '. $Page -> firstRow . ',' . $Page -> listRows;
		//echo $sql;exit;
		$list = $user -> query($sql);		

		$this -> assign('list', $list);
		
		$this -> assign('pageLink', $show);
		
		$this -> display();
	
	}
	
	public function add(){
	
		$this -> display();
	
	}
	
	public function adduser(){
	
		$flag = true;
	
		$data = I('post.');
		
		if(empty($data['name'])){
		
			$this -> error('用户名不能为空');
		
		}
		
		if(empty($data['password'])) $this -> error('密码不能为空');
		
		if($data['password'] != $data['repassword']) $this -> error('两次密码不一致');
		
		if(empty($data['email'])) $this -> error('邮箱不能为空');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
	
		$user = D('user');
	
		if($this -> _checkName()){

			$this -> error('用户名重复');
				
		}else{
	
			$data['password'] = md5($data['password']);
				
			$time = date('Y-m-d H:i:s');
				
			$data['addtime'] = $time;
				
			$data['lastlogin'] = $time;
				
			$data['lastip'] = $_SERVER['REMOTE_ADDR'];
	
			$result = $user -> add($data);
	
			if(empty($result)) $flag = false;
	
		}
	
		if(!$flag){
	
			$this -> error('添加失败');
	
		}else $this -> success('添加成功', U('User/index'));
	
	}
	
	//检测用户名是否重复
	public function checkName(){
	
		if($this -> _checkName()) echo 0;
	
		else echo 1;
	
	}
	
	protected function _checkName(){
	
		$data = I('post.');
	
		$user = D('user');
	
		$id = intval($data['id']);
	
		if(!empty($id)){
	
			$result = $user -> query("select id from " . C('DB_PREFIX') . "user where name='" . $data['name'] . "' and id <> " . $id);
	
		}else $result = $user -> where(array('name' => $data['name'])) -> find();
	
		if(!empty($result)) return 1;
	
		else return 0;
	
	}
	
	public function edit(){
	
		$id = intval($_GET['id']);
	
		$data = D('user') -> where('id='. $id) -> find();
	
		$this -> assign('data', $data);
	
		$this -> display();
	
	}
	
	public function edituser(){
	
		$flag = true;
	
		$data = I('post.');
		
		if(empty($data['name'])){
		
			$this -> error('用户名不能为空');
		
		}
		
		if(empty($data['email'])) $this -> error('邮箱不能为空');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
	
		$user = D('user');
	
		if($this -> _checkName()){

			$this -> error('用户名重复');
				
		}else{
	
			$result = $user -> save($data);
	
			if(empty($result)) $flag = false;
	
		}
	
		if(!$flag){
	
			$this -> error('编辑失败');
	
		}else $this -> success('编辑成功', U('User/index'));
	
	}
	
	public function editpwd(){
	
		$id = intval($_GET['id']);
	
		$data = D('user') -> where('id='. $id) -> find();
	
		$this -> assign('data', $data);
	
		$this -> display();
	
	}
		
	public function edituserpwd(){
	
		$flag = true;
	
		$data = I('post.');
		
		if(empty($data['password'])){
		
			$this -> error('原密码不能为空');
		
		}

		if(empty($data['newpassword']) || $data['newpassword'] != $data['renewpassword']) $this -> error('新密码不一致');		

		if(!empty($data['id'])){
	
			$user = D('user');
				
			$thisUser = $user -> where(array('id' => $data['id'])) -> find();

			if(!empty($thisUser) && $thisUser['password'] == md5($data['password'])){
	
				$result = $user -> save(array('id' => $data['id'], 'password' => md5($data['newpassword'])));
	
				if(empty($result)) $flag = false;
	
			}else $flag = false;
	
		}else $flag = false;
	
		if(!$flag){
	
			$this -> error('修改失败');
	
		}else $this -> success('修改成功', U('User/index'));
	
	}	
		
	public function del(){
	
		$id = $_GET['id'];
	
		if(is_array($id)) $id = implode(',', $id);
	
		$result = 1;
	
		if(preg_match('/\d(\,\d)*/', $id)){
	
			$user = D('user');
	
			$result = $user -> delete($id);
	
			$result = $result > 0 ? 1 : 0;
	
		}else $result = 0;
	
		if($result) $this -> success('删除成功', U('User/index'));
	
		else $this -> error('删除失败');
	
	}
	
}