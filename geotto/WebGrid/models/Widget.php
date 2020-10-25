<?php
include_once("Basic.php");
 
 class Widget extends Basic{
	 function __construct($dbc){
		parent::__construct($dbc); 
	}
	
	//添加控件
	function addWidget($name, $link, $height = null){
		$height = ($height == null)?'NULL':$height;
		
		$query = "INSERT INTO widget(name,link,height) VALUES('$name','$link', $height)";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			throw new Exception($query);
		}
		
		$query = "SELECT LAST_INSERT_ID() AS id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1){
			return -1;
		}
		
		$row = mysqli_fetch_array($result);
		return $row['id'];
	}
	
	//获取控件列表
	function getWidgets($order, $from=0, $count=-1){
		if($count == -1){
			$query = "SELECT * FROM widget ORDER BY $order";
		}else{
			$query = "SELECT * FROM widget ORDER BY $order LIMIT $from,$count";
		}
		
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num > 0){
			$widgets = array();
			while($row = mysqli_fetch_array($result)){
				$widgets[] = $this->row2array($row);
			}
			return $widgets;
		}
		
		return null;
	}
	
	//删除控件
	function delWidget($id){
		$query = "DELETE FROM widget WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			throw new Exception($query);
		}
	}
	
	//获取控件
	function getWidget($id){
		$query = "SELECT * FROM widget WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1){
			return null;
		}
		
		$row = mysqli_fetch_array($result);
		return $this->row2array($row);
	}
	
	private function row2array($row){
		return array(
			'id'=>$row['id'],
			'name'=>$row['name'],
			'link'=>$row['link'],
			'height'=>$row['height']
		);
	}
}
?>
