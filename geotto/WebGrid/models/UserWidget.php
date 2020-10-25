<?php
include_once("Basic.php");
 
 class UserWidget extends Basic{
	 function __construct($dbc){
		parent::__construct($dbc); 
	}
	
	//删除控件
	function delWidget($widget){
		$query = "DELETE FROM user_widgets WHERE widget=$widget";
		$result = mysqli_query($this->dbc, $query);
	}
	
	//添加控件
	function addWidget($user, $widget){
		$query = "INSERT INTO user_widgets(user, widget) VALUES($user, $widget)";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			throw new Exception($query);
		}
		
		$query = "SELECT LAST_INSERT_ID() AS id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1)
			return -1;
			
		$row = mysqli_fetch_array($result);
		return $row['id'];
	}
	
	//获取控件列表
	function getWidgets($user){
		$query = "SELECT * FROM user_widgets WHERE user=$user";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num > 0){
			$widgets = array();
			while($row = mysqli_fetch_array($result)){
				$widgets[] = $row['widget'];
			}
			
			return $widgets;
		}
		
		return null;
	}
	
	//查看控件是否已添加
	function exists($user, $widget){
		$query = "SELECT * FROM user_widgets WHERE user=$user AND widget=$widget";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num < 1){
			return false;
		}
		
		return true;
	}
	
	//移除控件
	function removeWidget($user, $widget){
		$query = "DELETE FROM user_widgets WHERE user=$user AND widget=$widget";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			throw new Exception($query);
		}
	}
}
