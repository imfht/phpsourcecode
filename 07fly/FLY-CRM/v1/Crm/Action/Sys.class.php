<?php
/*
 * 系统参数配置类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Sys extends Action{	
	
	private $cacheDir='';//缓存目录
	
	public function __construct() {
		 _instance('Action/Auth');
	}
	
	public function main(){

	}
	public function Index(){
		return _instance('Action/Index');
	}	
	public function User(){
		return _instance('Action/User');
	}
	public function Common(){
		return _instance('Extend/Common');
	}
	public function File() {	
		return _instance('Extend/File');
	}		
	public function Temp(){
		return _instance('Action/Temp');
	}
	
	//系统常规设置
	public function sys_config(){
		if(empty($_POST)){
			$config = $this->get_sys_info();
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$config));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('sys/sys_config.html');					
		}else{
			foreach($_POST as $key=>$v){
				$sql="INSERT INTO fly_sys_config(name,value) VALUES('$key','$v') 
						ON DUPLICATE KEY UPDATE value='$v'";
				$this->C($this->cacheDir)->update($sql);
			}
			$this->L("Common")->ajax_json_success("操作成功");
		}
	}

	//得到系统配置参数
	public function get_sys_info(){
		$sql 	= "select * from fly_sys_config;";
		$list	= $this->C($this->cacheDir)->findAll($sql);
		$assArr = array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$assArr[$row["name"]] = $row["value"];
			}
		}
		return $assArr;		
	}
	//系统密码设置
	public function sys_password_modify(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('sys/sys_password_modify.html');					
		}else{
			$oldpassword	= trim($_POST["oldpassword"]);
			$newpassword	= trim($_POST["newpassword"]);
			$newpassword1	= trim($_POST["newpassword1"]);

			if( $newpassword != $newpassword1 ){
				$this->L("Common")->ajax_json_error("两次密码不一样,请细心检查是否因大小写原因造成");	
				exit;
			}
			$sql= "select id from fly_sys_user where account='".SYS_USER_ACCOUNT."' and password='$oldpassword'";
			$one= $this->C($this->cacheDir)->findOne($sql);
			if(!empty($one)){ 
				$sql = "update fly_sys_user set password='$newpassword' where account='".SYS_USER_ACCOUNT."';";
				if($this->C($this->cacheDir)->update($sql)>=0){
					$this->L("Common")->ajax_json_success("操作成功");
					exit;
				}
			}else{
				$this->L("Common")->ajax_json_error("输入的旧密码不正确,请细心检查是否因大小写原因造成");
				exit;
			}
		}
	}
	
	//获取操作日志
	public function sys_log(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;		

		//用户查询参数
		$searchKeyword= $this->_REQUEST("searchKeyword");
		$searchValue  = $this->_REQUEST("searchValue");
		$startdate    = $this->_REQUEST("startdate");
		$enddate	  = $this->_REQUEST("enddate");	
		$editor	  	  = $this->_REQUEST("org_account");	
		
		$where 		  = "0=0 ";
		if(!empty($searchValue)){
			$where .= " and $searchKeyword like '%$searchValue%'";
		}	
		if($startdate){
			$where .=" and adddatetime>'$startdate' ";
		}
		if($enddate){
			$where .=" and adddatetime<='$enddate' ";
		}	
		if($editor){
			$where .=" and editor='$editor' ";
		}				
		$countSql	= "select * from fly_sys_log where $where";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;	
		$sql		= "select * from fly_sys_log  where $where order by id desc limit $beginRecord,$numPerPage";	
		$list		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循环
		$assignArray=array('list'=>$list,'searchKeyword'=>$searchKeyword,'searchValue'=>$searchValue,
							'startdate'=>$startdate,'enddate'=>$enddate,'editor'=>$editor,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage
							);				
					
		return $assignArray;
	}
	//调用显示
	public function sys_log_show(){
		$list	= $this->sys_log();
		$smarty = $this->setSmarty();
		$smarty->assign($list);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('sys/sys_log.html');			
	}
	
	public function sys_log_add($info,$editor=null){
		$nowtime = date("Y-m-d H:i:s",time());
		if(empty($editor)) $editor  = SYS_USER_ACCT;
		$ip		 = $this->Common()->get_client_ip();
		$sql 	 = "insert into fly_sys_log(ipaddr,content,editor,adddatetime) values('$ip','$info','$editor','$nowtime')";
		if($this->C($this->cacheDir)->update($sql)<=0){
			return false;
		}else{
			return true;
		}
	}

	
	//删除选中记录
	public function sys_log_del (){
		$id	  = $this->_REQUEST("ids");	
		$sql="delete from fly_sys_log where id in (".$id.");";											 
		if($this->C($this->cacheDir)->update($sql)>=0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/Sys/sys_log_show/');	
		}	
	}	
	
	//删除全部记录
	public function sys_log_del_all (){
		$sql="delete from fly_sys_log";							 
		if($this->C($this->cacheDir)->update($sql)>=0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/Sys/sys_log_show/');	
		}	
	}
	
	//系统栏目和权限列表
	public function sys_menu($id=null){
		$list 	   = require(EXTEND . 'Menu.php');
		$role_menu = array();
		$role_mod  = array();
		if($id){
			$result = $this->sys_role_power_one($id);
			$role_menu =explode(',',$result["sys_menu"]); 
			$role_mod  =explode(',',$result["sys_action"]); 
		}
		$string  = "<table id=menu_power>";
		$string .= "<tr bgcolor='#FBF5C6'><td>栏目</td><td>菜单</td></tr>";
		$cnt	 = 1;
		if(is_array($list)){
			foreach($list as  $key=>$row){
				$bgcolor =($cnt%2==0)?"#FBF5C6":"#F9F9F9";
				$string .="<tr bgcolor=".$bgcolor."><td width='10%'>".$row["desc"]."<input type='checkbox' name='menuID[]' value='".$key."' " ;
				if(in_array($key,$role_menu)) $string .= " checked";
				$string .= " onclick='test(this);'></td><td>";
					foreach($row["menuitem"] as $item_key=>$item){
						$string .= "<table><tr><td width='15%'><input type='checkbox' name='menuID[]' value='".$item_key."' " ;
						if(in_array($item_key,$role_menu)) $string .= " checked";
						$string .= "> ".$item["desc"]."</td><td align=left>";	
							if(is_array($item["mod"])){
								foreach($item["mod"] as $mod_key=>$m_va){
									$string .= "<li style='list-style:none;width:100px;float:left;'><input type='checkbox' name='modID[]' value='".$mod_key."' " ;
									if(in_array($mod_key,$role_mod)) $string .= " checked";
									$string .= "> ".$m_va."</li>";							
								}	
							}
						$string .= "</td></tr></table>";				
					}
				$cnt++;
				$string .= "</td></tr>";			
			}
			$string .= "</table>";
		}
		return $string;	
	}


	//系统升级导入 
	public function sys_upgrade(){
		$filename = $this->_REQUEST("filename");
		$ver	  = $this->L("Data")->version();
		$version  = $ver["version"];
		
/*		//在线数据
		$server	  = $this->L("Data")->upgrade_server();
		$updateurl="$server/aaaupdate.php?ver=$version";
		$handle   = fopen ($updateurl,"rb");
		$contents = "";
		do {
			$data = fread($handle, 8192);
			if (strlen($data) == 0) break;
				$contents .= $data;
		} while(true);
		fclose ($handle);
		$onlist = json_decode($contents,true);*/
		$onlist = array();

		//本地数据
		$dirname= $this->L("Upload")->upload_upgrade_path();
		$File   = $this->File()->list_dir_info($dirname,$is_all=FALSE,$exts='',$sort='DESC');
		foreach($File as $onefile){
			$one  =$this->File()->dir_replace($onefile);
			$info =$this->File()->list_info($one);
			$info["size"] = $this->File()->byte_format($info["size"]);
			$info["ctime"] = date("Y-m-d H:i:s",$info["ctime"]);
			$list[]=$info;
		}
		
		$smarty =$this->setSmarty();
		$smarty->assign(array("list"=>$list,'onlist'=>$onlist,'ver'=>$ver));//框架变量注入同样适用于smarty的assign方
		$smarty->display('sys/sys_upgrade.html');			
	
	}
	
	public function sys_upgrade_local(){
		
		$step		= $this->_REQUEST("step");
		$filename  = $this->_REQUEST("filename");
		//本地数据，源文件所在地
		$dirname= $this->L("Upload")->upload_upgrade_path();
		$zipfile= $this->L("File")->dir_replace($dirname."/".$filename);
		
		if(empty($step)){
			$txt 	="<dd>升级文件为：$zipfile </dd>";		
			$step	=1;
			$sbtxt	="下一步备份当前系统";
			
		}elseif($step==1){
			$rtn	=$this->sys_upgrade_backup();
			$txt 	="<p>备份完成!</p><p>当前版本程序备份文件为：</p><p>$rtn</p>";		
			$step	=2;	
			$sbtxt	="下一步升级系统";							
		}elseif($step==2){
			/*
			$savepath 	=$this->L('File')->dir_replace(APP_ROOT);
			$archive	=$this->L("PclZip","$zipfile");
			if ($archive->extract(PCLZIP_OPT_PATH, "$savepath") == 0) {
				exec("tar -zxvf $zipfile -C /");
				//die("Error : ".$archive->errorInfo(true));
			}	
			*/		
			$txt 	="<p>系统升级完成!</p><p>程序已经覆盖当前系统目录</p> ";		
			$step	=3;	
			$sbtxt	="当前系统升级完成,下一步升级数据库结构";			
		}elseif($step=4){
			if($this->L('Data')->upgrade()){
				$txt 	="<p> 1,数据库升级完成!</p><p> 2,数据库升级成功</p> ";		
				$step	=5;	
				$sbtxt	="当前数据库升级完成,下一步清除系统缓存文件";	
			}
		}elseif($step=5){
			$rmdir = CACHE."/templates_c/";
			if($this->L('File')->remove_dir($rmdir)){
				$txt 	="<p> 1,数据库升级完成!</p><p> 2,数据库升级成功</p> ";		
				$step	=5;	
				$sbtxt	="当前数据库升级完成,下一步清除系统缓存文件";	
			}
		}
		
		//备份操作
		$smarty =$this->setSmarty();
		$smarty->assign(array('txt'=>$txt,'step'=>$step,'sbtxt'=>$sbtxt,'filename'=>$filename));
		$smarty->display('sys/sys_upgrade_local.html');				
	}	
	
	public function sys_upgrade_online(){
		$step 	    = $this->_REQUEST("step");
		$version	= $this->_REQUEST("version");
		$downpath	= $this->L("Upload")->upload_upgrade_path();
		$downpath 	= $this->L("File")->dir_replace($downpath);
		
		if(empty($step)){
			$this->sys_upgrade_online_down();//从网络地下载
			$txt 	="<dd>升级文件下载到本地目录：$downpath </dd>";		
			$step	=1;
			$sbtxt	="下一步备份当前系统";
			
		}elseif($step==1){
			$rtn	=$this->sys_upgrade_backup();
			$txt 	="<p>备份完成!</p><p>当前版本程序备份文件为：</p><p>$rtn</p>";		
			$step	=2;	
			$sbtxt	="下一步升级系统";	
									
		}elseif($step==2){
			$zipfile 	= $downpath."$version";
			$savepath 	= $this->L('File')->dir_replace(APP_ROOT);
			$archive=$this->L("PclZip","$zipfile");
			if ($archive->extract(PCLZIP_OPT_PATH, "$savepath") == 0) {
				exec("tar -zxvf $zipfile -C /");
				//die("Error : ".$archive->errorInfo(true));
			}			
			$txt ="<p>系统升级完成!</p><p>程序已经覆盖当前系统目录</p> ";		
			$step=3;	
			$sbtxt	="当前系统升级完成";			
		}
		
		//备份操作
		$smarty =$this->setSmarty();
		$smarty->assign(array('txt'=>$txt,'step'=>$step,'version'=>$version,'sbtxt'=>$sbtxt));
		$smarty->display('sys/sys_upgrade_online.html');			
		
	}

	//下载升级文件
	public function sys_upgrade_online_down($version=null){
		$version	= $this->_REQUEST("version");
		$downpath	= $this->L("Upload")->upload_upgrade_path();
		$downpath 	= $this->L("File")->dir_replace($downpath);
		$this->File()->create_dir($downpath);
		
		$server	    = $this->L("Data")->upgrade_server();	//获取网络信息
		$url		= "$server/aaaupdate.php?ver=$version&act=down";
		$pakurl		=$this->L("File")->read_file($url);//得到服务器返回包的地址
		
		$finfo 	= $this->L("File")->get_file_type("$pakurl");
		$nfile	="$server/$pakurl";
		$result = $this->L("File")->down_remote_file($nfile,$downpath,$finfo['basename'],$type=0);	
		return true;		
	}
	
	public function sys_upgrade_online_to_local(){
		$version	= $this->_REQUEST("version");
		$this->sys_upgrade_online_down();
		$this->L("Common")->ajax_json_success("下载成功","1","/Sys/sys_upgrade/");
	}
	
	//升级备份原程序
	public function sys_upgrade_backup(){
		$source_path = $this->L('File')->dir_replace(APP_ROOT);
		$backup_path = $this->L("Data")->backup_upgrade_path();
		$backup_path = $backup_path.date("YmdHis",time())."/";
		$dirarr		 = array('Action','Extend','View');
		foreach($dirarr as $dir){
			$backup_dir  = $backup_path."{$dir}/";	
			$rtn[]=$this->L('File')->create_dir($backup_dir);
			$rtn[]=$this->L('File')->handle_dir($source_path."/{$dir}",$backup_dir,'copy',true);
		}	
		if(in_array("0", $rtn, TRUE)){
			return false;
		} else {
			return $backup_path;
		}
/*		$backup_zfile= "upgrade_backup-".date("YmdHis",time()).".zip";
		$backup_zfile= $this->L('File')->dir_replace($backup_path.$backup_zfile);
		$archive=$this->L("PclZip","$backup_zfile");
		$v_list = $archive->create($source_path);  
		if ($v_list == 0) {  
			die("Error : ".$archive->errorInfo(true));  
		}{
			return 	$backup_zfile;
		} */ 		
	}
	
	//导入升级文件删除
	public function sys_upgrade_del(){
		$dirname  = $this->L("Upload")->upload_upgrade_path();
		$filename = ($_GET["filename"])?$_GET["filename"]:$_POST["filename"];
		$this->File()->unlink_file($dirname.$filename);
		$this->L("Common")->ajax_json_success("删除成功","1","/Sys/sys_upgrade/");		
	}

}//end class
?>