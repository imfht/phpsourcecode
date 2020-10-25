<?php
include_once("Basic.php");
/*
 * 该类用于管理管理员数据库
 * */
 
 class Administrator extends Basic{
	 function __construct($dbc){
		parent::__construct($dbc); 
	}
	
	//登录
	function login($name, $password){
		$query = "SELECT * FROM administrator WHERE name='$name' AND password=SHA1('$password')";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1){
            return null;
		}
		
		$row = mysqli_fetch_array($result);
		$admin = array(
			'id'=>$row['id'],
			'name'=>$row['name']
		);
		
		return $admin;
	}
}
 ?>
