<?php
/*
 * 数据配置类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Data extends Action{	
	private $cacheDir='';//缓存目录
	
	public function upgrade_server(){
		return 	"http://aaaupgrade.host.07fly.net";
	}
	
	public function authorize_server_url(){
		//return "http://localhost:901/boss/";
		return "http://serial.host.07fly.top";	
	}
	
	public function backup_mysql_path(){
		$path = CACHE."backup/mysql/";
		$this->L("File")->create_dir($path);
		return $path;
	}	
	
	public function backup_upgrade_path(){
		$path = CACHE."backup/upgrade/";
		$this->L("File")->create_dir($path);
		return $path;
	}
	
	
	//得到系统的版本号
	public function version(){
		return array("version"=>1.01,"bdtime"=>'20180101');
	}
	
	public function upgrade(){
		$sqllist = $this->L("DataSql")->sql_event();
		foreach($sqllist as $sql){
			$this->C($this->cacheDir)->update($sql);			
		}
		//初始化权限
		$this->sys_role_init();
		
		return true;	
	}

	//初始化系统权限功能
	public function sys_role_init() {
		
		//增加权限名
		$sqllist[]="INSERT IGNORE INTO `fly_sys_role` (`id`, `sort`, `name`, `intro`) 
											   VALUES (1, 1, '超级管理员', '超组管理员');";
		//增加默认管理员
		$sqllist[]="
			 INSERT IGNORE INTO `fly_sys_user` (`id`, `identity`, `account`, `password`, `name`, `gender`, 
							`tel`, `mobile`, `qicq`, `zipcode`, `address`, `email`, 
							`roleID`, `deptID`, `intro`, `adt`) 
			 VALUES (1, 'SYS0001', '07fly', '07fly.com', '零起飞', '1',
			 			 '028-61833149', '18030402705', '1871720801', '61000', '成都市', 'goodmuzi@qq.com', 
						 '1', NULL, '零起飞网络工作室，是一个具有专业水平和非凡创意的制作者的组合~', NULL);";		
		//增加默认权限参数
		$sqllist[]="
				INSERT IGNORE INTO `fly_sys_power` (`id`, `master`, `master_value`, `access`, `access_value`, `operation`) 
									 VALUES (1, 'role', '1', 'SYS_MENU', '1', NULL);";
		$sqllist[]="
				INSERT IGNORE INTO `fly_sys_power` (`id`, `master`, `master_value`, `access`, `access_value`, `operation`) 
									 VALUES (2, 'role', '1', 'SYS_METHOD', '1', NULL);";
		$sqllist[]="
				INSERT IGNORE INTO `fly_sys_power` (`id`, `master`, `master_value`, `access`, `access_value`, `operation`) 
									 VALUES (3, 'role', '1', 'SYS_AREA', '1', NULL);";
		
		//增加管理员和权限关联
		$sqllist[]=" update `fly_sys_user`  set roleID='1' where id='1'";
		
		//初始权限参数
		$menuArr=$this->C($this->cacheDir)->findAll("select id from fly_sys_menu");
		foreach($menuArr as $v){
			$menu[]=$v["id"];
		}
		
		/*暂时没有地区
		$areaArr=$this->C($this->cacheDir)->findAll("select id from fly_tp_area");
		foreach($areaArr as $v){
			$area[]=$v["id"];
		}*/
		$modArr=$this->C($this->cacheDir)->findAll("select value from fly_sys_method");
		foreach($modArr as $v){
			$mod[]=$v["value"];
		}
		$menu=implode(",",$menu);
		//$area=implode(",",$area);
		$mod =implode(",",$mod);

		$sqllist[]="update `fly_sys_power` set access_value='$menu' 
					where master='role' and master_value='1' and access='SYS_MENU'";
		/*$sqllist[]="update `fly_sys_power` set access_value='$area' 
					where master='role' and master_value='1' and access='SYS_AREA'";*/
		$sqllist[]="update `fly_sys_power` set access_value='$mod'  
					where master='role' and master_value='1' and access='SYS_METHOD'";
		
		foreach($sqllist as $onesql){
			$this->C($this->cacheDir)->update($onesql);		
		}
			

	}	
	
			
}// end class

?>