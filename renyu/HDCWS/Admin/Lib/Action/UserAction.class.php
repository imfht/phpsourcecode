<?php

//用户类
class UserAction extends GlobalAction {
	
	function _initialize(){
	
		if(!($this -> isSuper())) exit;
	
	}	
	
	public function getList(){

		$start = intval($_GET['start']);
		
		$limit = intval($_GET['limit']);
		
		$key = $_GET['key'];
		
		$condition = ' where (p.name like "%' . $key . '%" or p.email like "%' . $key . '%") and (p.name <> "admin") ';		
		
		if(empty($start)) $start = 0;
		
		if(empty($limit)) $limit = 20;
		
		$user = D('user');
		
		$count = $user -> query('select count(*) counts from ' . C('DB_PREFIX') . 'user p ' . $condition);

		$count = empty($count) ? 0 : $count[0]['counts'];
		
		$sql = 'select id,name,email,addtime,lastlogin,lastip,status from ' . C('DB_PREFIX') . 'user p ' . $condition . ' order by addtime desc limit '. $start . ',' . $limit;
		
		$list = $user -> query($sql);
		
		echo json_encode(array('list' => $list, 'total' => $count, 'success' => true));
	
	}
	
	//添加
	public function add(){
	
		$flag = true;
	
		$data = I('post.');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
	
		$user = D('user');
		
		if($this -> _checkName()){
			
			$flag = false;
			
		}else{
		
			$data['password'] = md5($data['password']);
			
			$time = date('Y-m-d H:i:s');
			
			$data['addtime'] = $time;
			
			$data['lastlogin'] = $time;
			
			$data['lastip'] = $_SERVER['REMOTE_ADDR'];
		
			$result = $user -> add($data);
		
			if(empty($result)) $flag = false;
		
		}
	
		echo json_encode(array('success' => $flag));
	
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
	
	//编辑
	public function edit(){
	
		$flag = true;
	
		$data = I('post.');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
	
		$user = D('user');
	
		$result = $user -> save($data);
	
		if(empty($result)) $flag = false;
	
		echo json_encode(array('success' => $flag));
	
	}
	
	//修改密码
	public function modPwd(){
		
		$flag = true;
		
		$data = I('post.');
		
		if(!empty($data['id']) && !empty($data['password'])){
		
			$user = D('user');
			
			$thisUser = $user -> where(array('id' => $data['id'])) -> find();
			
			if(!empty($thisUser) && $thisUser['password'] == md5($data['old'])){

				$result = $user -> save(array('id' => $data['id'], 'password' => md5($data['password'])));
				
				if(empty($result)) $flag = false;
				
			}else $flag = false;
		
		}else $flag = false;
		
		echo json_encode(array('success' => $flag));
		
	}
	
	//删除
	public function del(){
	
		$id = $_POST['id'];
	
		$result = 1;
	
		$prefix = C('DB_PREFIX');
	
		if(preg_match('/\d(\,\d)*/', $id)){
	
			$user = D('user');

			$result = $user -> delete($id);
				
			$result = $result > 0 ? 1 : 0;
				
		}else $result = 0;
	
		echo $result;

	}
	
}