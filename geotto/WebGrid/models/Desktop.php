<?php
include_once("Basic.php");
/*
 * 该类用于管理桌面数据库
 * */
 
 class Desktop extends Basic{
	 function __construct($dbc){
		parent::__construct($dbc); 
	}
	
	//添加桌面
	function addDesktop($user, $copy=true){
		if($copy){
			//获取默认桌面内容
			$sites = $this->getSites(1);
			$str_sites = implode(",", $sites);			
			$query = "INSERT INTO desktop(user, sites) VALUES($user, '$str_sites')";
		}else{
			$query = "INSERT INTO desktop(user) VALUES($user)";
		}
		
        $result = mysqli_query($this->dbc, $query);
        $num = mysqli_affected_rows($this->dbc);
        if($num != 1){
            throw new Exception($query);
        }

        //获取id
        $query = "SELECT LAST_INSERT_ID() as id";
        $result = mysqli_query($this->dbc, $query);
        $num = mysqli_num_rows($result);
        if($num != 1){
            throw new Exception($query);
        }

        $row = mysqli_fetch_array($result);
        $id = $row['id'];

        return $id;
	}
	
	//移除桌面
	function removeDesktop($id, $user){
		$query = "DELETE FROM desktop WHERE id=$id AND user=$user";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1){
			return false;
		}
		
		return true;
	}
	
	//获取桌面网址
	function getSites($id){
		$query = "SELECT sites FROM desktop WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_num_rows($result);
		if($num != 1){
				return null;
		}
		
		//解析网址列表
		$row = mysqli_fetch_array($result);
		if($row['sites'] == null)
			return null;
		$sites = $this->parseSites($row['sites']);
		
		return $sites;
	}
	
	//添加图标
	function addSite($id, $site, $index){
		$sites = $this->getSites($id);
		if($sites == null){
				$sites = array();
		}
		
		//插入列表
		if($index > count($sites))
			return false;
        //插入尾部
        if($index == -1){
            $index = count($sites);
        }
		array_splice($sites, $index, 0, array(0=>$site));
		
		//更新数据库
		$strSites = implode(",", $sites);
		$query = "UPDATE desktop SET sites='$strSites' WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1 && $num != 0){
				throw new Exception($query);
		}
		
		return true;
	}
	
	//移除网址图标
	function removeSite($id, $index){
		$sites = $this->getSites($id);
		
		if($sites == null || $index >= count($sites))
			return false;
		unset($sites[$index]);
		
		//更新数据库
		$strSites = implode(",", $sites);
		$query = "UPDATE desktop SET sites='$strSites' WHERE id=$id";
		$result = mysqli_query($this->dbc, $query);
		$num = mysqli_affected_rows($this->dbc);
		if($num != 1 && $num != 0){
				throw new Exception($query);
		}
		
		return true;
	}
     
     //解析网址列表
     private function parseSites($strSites){
		$sites = explode(",", $strSites);
         
         return $sites;
     }
}
 ?>
