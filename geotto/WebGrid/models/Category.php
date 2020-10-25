<?php
include_once("Basic.php");
/*
 * 该类用于管理分类数据库
 * */
 
 class Category extends Basic{
	 function __construct($dbc){
		parent::__construct($dbc); 
	}
	
	//获取分类
	function getCategories(){
		$query = "SELECT * FROM category";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num > 0){
			$categories = array();
			while($row = mysqli_fetch_array($result)){
				$categories[] = array(
					'id'=>$row['id'],
					'name'=>$row['name']
				);
			}
			
			return $categories;
		}
		
		return null;
	}
}
 ?>
