<?php
include_once("Basic.php");
/*
 * 该类用于管理网址数据库
 * */
 
 class Site extends Basic{
	 function __construct($dbc){
		parent::__construct($dbc); 
	}
	
	//根据类型获取网址
	function getSitesByCategory($category, $from=0, $count=-1){
        if($count = -1){
            $query = "SELECT * FROM site WHERE category=$category ORDER BY population";
        }else{
		  $query = "SELECT * FROM site WHERE category=$category ORDER BY population LIMIT $from,$count";
        }
        
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num > 0){
			$sites = array();
			while($row = mysqli_fetch_array($result)){
				$sites[] = array(
					'id'=>$row['id'],
					'url'=>$row['url'],
					'name'=>$row['name'],
					'icon'=>$row['icon'],
					'population'=>$row['population'],
					'category'=>$row['category']
				);	
			}
			
			return $sites;
			
		}
		
		return null;
	}
	
	//获取最受欢迎的网址
	function getPopSites($count){
		$query = "SELECT * FROM site ORDER BY population DESC LIMIT 0,$count";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num > 0){
			$sites = array();
			while($row = mysqli_fetch_array($result)){
				$sites[] = array(
					'id'=>$row['id'],
					'url'=>$row['url'],
					'name'=>$row['name'],
					'icon'=>$row['icon'],
					'population'=>$row['population'],
					'category'=>$row['category']
				);	
			}
			
			return $sites;
		}
		
		return null;
	}
	
	//获取网址
	function getSites($order, $from, $count){
		$query = "SELECT * FROM site ORDER BY $order LIMIT $from,$count";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num > 0){
			$sites = array();
			while($row = mysqli_fetch_array($result)){
				$sites[] = array(
					'id'=>$row['id'],
					'url'=>$row['url'],
					'name'=>$row['name'],
					'icon'=>$row['icon'],
					'population'=>$row['population'],
					'category'=>$row['category']
				);	
			}
			
			return $sites;
		}
		
		return null;
	}
	
	//根据关键词搜索
	function search($keyword){
		$query = "SELECT * FROM site WHERE name LIKE '%$keyword%'";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num > 0){
			$sites = array();
			while($row = mysqli_fetch_array($result)){
				$sites[] = array(
					'id'=>$row['id'],
					'url'=>$row['url'],
					'name'=>$row['name'],
					'icon'=>$row['icon'],
					'population'=>$row['population'],
					'category'=>$row['category']
				);	
			}
			
			return $sites;
		}
		
		return null;
	}
	
	//添加网址
	function addSite($name, $url, $category=null, $icon=null){
		$icon = ($icon == null)?'NULL':"'$icon'";
        $category = ($category == null)?"NULL":$category;
        
		$query = "INSERT INTO site(url, name, icon, category) VALUES('$url', '$name', $icon, $category)";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			throw new Exception($query);
		}
		
		//获取编号
		$query = "SELECT LAST_INSERT_ID() AS id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1){
            return -1;
		}
		
		$row = mysqli_fetch_array($result);
		return $row['id'];
	}
	
	//增加受欢迎度
	function stepPop($id){
		$query = "SELECT population FROM site WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1){
			throw new Exception(NO_RECORDS);
		}
		
		$row = mysqli_fetch_array($result);
		$population = $row['population'];
		$population ++;
		
		//更新数据库
		$query = "UPDATE site SET population=$population WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			throw new Exception($query);
		}
		
		return $population;
	}
     
     //获取网址
     function getSite($id){
		$query = "SELECT * FROM site WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1){
            return null;
        }
         
        $row = mysqli_fetch_array($result);
        $site = array(
            'id'=>$row['id'],
            'url'=>$row['url'],
            'name'=>$row['name'],
            'icon'=>$row['icon'],
            'population'=>$row['population'],
            'category'=>$row['category']
        );
         
        return $site;
     }
	
	//删除网址
	function delSite($id){
		$query = "DELETE FROM site WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			return false;
		}
		
		return true;
	}
     
     //获取分类网址数量
}
 ?>
