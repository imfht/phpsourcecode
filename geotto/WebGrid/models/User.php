<?php
include_once("Basic.php");
/*
 * 该类用于管理用户数据库
 * */
 
 class User extends Basic{
	 function __construct($dbc){
		parent::__construct($dbc); 
	}
	
	//查看用户是否存在
	function exists($name){
		$query = "SELECT id FROM user WHERE name='$name'";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num > 0){
			return true;
		}
		
		return false;
	}
	
	//注册
	function register($name, $password){
		$query = "INSERT INTO user(name, password) VALUES('$name', SHA1('$password'))";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			throw new Exception($query);
		}
		
		$query = "SELECT LAST_INSERT_ID() as id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1){
			throw new Exception($query);
		}
		
		$row = mysqli_fetch_array($result);
		return $row['id'];
	}
	
	//登录
	function login($name, $password){
		$query = "SELECT * FROM user WHERE name='$name' AND password=SHA1('$password')";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1)
			return null;
		
		$row = mysqli_fetch_array($result);		
		return $this->row2array($row);
	}
	
	//检测用户编号是否合法
	function validId($id){
			$query = "SELECT * FROM user WHERE id=$id";
			$result = mysqli_query($this->dbc, $query);
			$num = mysqli_num_rows($result);
			return $num == 1?true:false;
	}
	
	//设置桌面背景
	function setBackground($id, $background){		
		$query = "UPDATE user SET background='$background' WHERE id='$id'";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 0 && $num != 1){
				throw new Exception($query);
		}
	}
	
	//获取桌面列表
	function getDesktops($id){
		$query = "SELECT desktops FROM user WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1){
				throw new Exception($query);
		}
		
		$row = mysqli_fetch_array($result);
		if($row['desktops'] == null)
			return null;
		$desktops = explode(SEP_I, $row['desktops']);
		
		return $desktops;
	}
	
	//添加桌面
	function addDesktop($id, $desktop){
		//获取当前桌面
		$desktops = $this->getDesktops($id);
		$strDesktops = ($desktops == null)?$desktop:(implode(SEP_I, $desktops).SEP_I.$desktop);
		
		$query = "UPDATE user SET desktops='$strDesktops' WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 0 && $num != 1){
				return false;
		}
		
		return true;
	}
	
	//删除桌面
	function delDesktop($id, $desktop){
		try{
			$desktops = $this->getDesktops($id);
			if($desktops == null){
				return false;
			}
		}catch(Exception $e){
					throw $e;
		}
		
		//删除桌面
		for($i=0;$i<count($desktops);$i++){
				if($desktops[$i] == $desktop){
						unset($desktops[$i]);
				}
		}
		
		//更新数据库
		$strDesktops = implode(SEP_I, $desktops);
		$strDesktops = ($strDesktops == "")?"NULL":"'$strDesktops'";
		$query = "UPDATE user SET desktops=$strDesktops WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
				return false;
		}
				
		return true;
	}
     
     //修改密码
     function changePassword($id, $old_password, $new_password){
        //验证旧密码
         $query = "SELECT * FROM user WHERE  password = SHA1('$old_password') AND id=$id";
         $result = mysqli_query($this->dbc, $query);
         $num = mysqli_num_rows($result);
         if($num != 1){
            return false;
         }
         
         //更新密码
         $query = "UPDATE user SET password=SHA1('$new_password') WHERE id=$id";
         $result = mysqli_query($this->dbc, $query);
         $num = mysqli_affected_rows($this->dbc);
         if($num != 1 && $num != 0){
            throw new Exception($query);
         }
         
         return true;
     }
     
     //自动登录
     function signIn($id, $key){
		$query = "SELECT * FROM user WHERE id=$id AND password='$key'";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1){
			return null;
		} 
		
		$row = mysqli_fetch_array($result);
		return $this->row2array($row);
	}
	
	//将记录转换为关联数组
	private function row2array($row){
		return array(
			'id'=>$row['id'],
			'name'=>$row['name'],
			'background'=>$row['background']
		);
	}
}
 ?>
