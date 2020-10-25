<?php
include_once("Basic.php");
/*
 * 该类用于管理桌面图标数据库
 * */
 
 class Icon extends Basic{
	 function __construct($dbc){
		parent::__construct($dbc); 
	}
	
	//添加图标
	function addIcon($url, $name='NULL'){
		$name = ($name == 'NULL')?'NULL':"'$name'";
		$query = "INSERT INTO icon(url, name) VALUES('$url', $name)";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			return false;
		}
		
		return true;
	}
	
	//获取图标
	function getIcons($from, $count){
		$query = "SELECT * FROM icon ORDER BY id LIMIT $from,$count";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num > 0){
			$icons = array();
			while($row = mysqli_fetch_array($result)){
				$icons[] = array(
					'id'=>$row['id'],
					'url'=>$row['url'],
					'name'=>$row['name']
				);
			}
			
			return $icons;
		}
		
		return null;
	}
	
	//按关键字搜索图标
	function search($keyword){
        if($keyword == null){
            $query = "SELECT * FROM icon";
        }else{
		  $query = "SELECT * FROM icon WHERE name LIKE '%$keyword%'";
        }
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num > 0){
			$icons = array();
			while($row = mysqli_fetch_array($result)){
				$icons[] = array(
					'id'=>$row['id'],
					'url'=>$row['url'],
					'name'=>$row['name']
				);
			}
			
			return $icons;
		}
		
		return null;
	}
	
	//删除图标
	function delIcon($id){
		$query = "DELETE FROM icon WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			return false;
		}
		
		return true;
	}
     
     //获取图标链接
     function getUrl($id){
        $query = "SELECT * FROM icon WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
         if($num != 1){
            return null;
         }
         
         $row = mysqli_fetch_array($result);
         return $row['url'];
     }
}
 ?>
