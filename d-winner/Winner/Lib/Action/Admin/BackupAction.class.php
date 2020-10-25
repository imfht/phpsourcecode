<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

class BackupAction extends Action {
	/**
		* 备份列表
		*@param $json    为NULL输出模板。为1时输出列表数据到前端，格式为Json
		*@examlpe 
	*/
    public function index($json=NULL){
		$Public = A('Index','Public');
		$Public->check('Backup',array('r'));
		import('ORG.Net.FileSystem');
		$path = new FileSystem();
		$path->root = ITEM;
		$path->charset = C('CFG_CHARSET');
		//main
		if(!is_int((int)$json)){
			$json = NULL;
		}
		if($json==1){
			$url = ROOT.'Conf/Backup';
			$info = $path->path($url,1);
			$new_info = array();
			foreach($info as $t){
				$t['size'] = round($path->dirSize(CONF_PATH.'Backup/'.$t['name'])/1204,2).' MB';
				$path->clearInfo();
				$t['addtime'] = date("Y-m-d H:i:s",$t['addtime']);
				$new_info[] = $t;
			}
			echo json_encode($new_info);
			unset($group,$info,$new_info,$path);
		}else{
			$mysql_version = mysql_get_server_info();
			$this->assign('mysql_version',$mysql_version);
			$this->display();
		}
		unset($Public);
    }
	
	
	/**
		* 备份数据库
		*@param $act   为1时输出数据表数
		*@examlpe 
	*/
	public function bakdb($act=NULL){	
		$Public = A('Index','Public');
		$role = $Public->check('Backup',array('c'));
		
		if($role<0){
			echo $role; exit;
		}
		
		//实例化文件系统操作类
		import('ORG.Net.FileSystem');
		$path = new FileSystem();
		$path->root = ITEM;
		$path->charset = C('CFG_CHARSET');
		
		$sql = A('Sql','Public');					//实例化sql类
		
		//main	
		if($act==1){
			$path->delFile(RUNTIME_PATH.'/backup.tmp');
			cookie('info_step',NULL);
			cookie('info_page',NULL);
			$arr_table['charset'] = I('charset');
			$arr_table['version'] = I('version');
			$arr_table['filesize'] = I('filesize');
			$arr_table['table'] = $sql->getTable();		//获取数据表
			$path->putFile(RUNTIME_PATH.'/database.tmp',serialize($arr_table));
			echo count($arr_table['table']);
		}
	}
	
	/**
		* 还原数据库
		*@param $act   为1时输出数据表数
		*@examlpe 
	*/
	public function redb($act=NULL){	
		$Public = A('Index','Public');
		$role = $Public->check('Backup',array('u'));
		if($role<0){
			echo $role; exit;
		}
		
		//实例化文件系统操作类
		import('ORG.Net.FileSystem');
		$path = new FileSystem();
		$path->root = ITEM;
		$path->charset = C('CFG_CHARSET');
		
		//main	
		if($act==1){
			$file = I('file');
			$realfile = CONF_PATH.'Backup/'.$file;
			$arr_table['path'] = $realfile;
			$arr_table['table'] = $path->nListPath($realfile);
			$path->putFile(RUNTIME_PATH.'/database.tmp',serialize($arr_table));
			echo count($arr_table['table']);
		}
	}
	
	
	/**
		* 显示备份、还原数据库流
		*@param $act   bak为备份、re为还原
		*@param $total  传入表总数
		*@param $go  为1时，获取post
		*@examlpe 
	*/
	public function show($act,$total=NULL,$go=-1,$page=-1){
		$Public = A('Index','Public');
		$Public->check('Backup',array('c'));		
		$sql = A('Sql','Public');						//实例化sql类
			
		//实例化文件系统操作类
		import('ORG.Net.FileSystem');
		$path = new FileSystem();
		$path->root = ITEM;
		$path->charset = C('CFG_CHARSET');
		
		set_time_limit(1000);
		
		//main	
		if($go>=0){
			if($act=='bak'){
				$str_table = $path->getFile(RUNTIME_PATH.'/database.tmp');
				$arr_table = unserialize($str_table);
				if($go==count($arr_table['table'])){
					cookie('badate',NULL);
					$path->delFile(RUNTIME_PATH.'/database.tmp');
					$path->delFile(RUNTIME_PATH.'/backup.tmp');
					cookie('info_step',NULL);
					cookie('info_page',NULL);
					echo '所有表已完成备份！|0|0'; exit;
				}
				if(cookie('badate')){
					$badate = cookie('badate');
				}else{
					$badate = date("Y-m-d_His");
					cookie('badate',$badate);
				}
				$bak_dir = ROOT.'/Conf/Backup/'.$badate;
				if(!file_exists($bak_dir)){
					$path->putDir($bak_dir,0777);
				}
				
				$strfile = '';
				$table = $arr_table['table'][$go];
				$tb = str_replace(C('DB_PREFIX'),'#@_',$table);
				
				$result = M();
				
				$str_info = $path->getFile(RUNTIME_PATH.'/backup.tmp');
				
				if($str_info){
					$str_info = slashes($str_info);
					$info = unserialize($str_info);
					$page = cookie('info_page')?cookie('info_page'):0;
					$p =  cookie('info_step')?cookie('info_step'):1;
				}else{
					$count = $result->table($table)->count();
					$total = ceil($count/10000);
					if(cookie('info_page')){
						$page = cookie('info_page');
					}else{
						$page = 0;
					}
					
					if($count>10000){
						$info = $result->table($table)->limit($page*10000,10000)->select();
						if($page<$total){
							if($page==0){
								$p = 1;
							}else{
								$p = cookie('info_step')?cookie('info_step'):1;
							}
							$page++;
							cookie('info_page',$page);
							if($p==1){
								$strfile .= "DROP TABLE IF EXISTS `".$tb."`;\r\n";
								$table_field = $sql->getField($table);		//获取表结构
								
								//替换数据表名
								$mysql = mysql_get_server_info();
								$get_field = preg_replace("/AUTO_INCREMENT=[0-9]+\s+/","",$table_field);
								if($arr_table['version']==4.1 && $mysql>4.1){
									$get_field = preg_replace("/ENGINE=\b.{2,}\b DEFAULT CHARSET=\S+/",'ENGINE=MyISAM DEFAULT CHARSET='.$arr_table['charset'],$get_field);
								}elseif($arr_table['version']==4.1 && $mysql<4.1){
									$get_field = preg_replace("TYPE=\b.{2,}\b",'ENGINE=MyISAM DEFAULT CHARSET='.$arr_table['charset'],$get_field);
								}elseif($arr_table['version']==4.0 && $mysql>4.1){
									$get_field = preg_replace("/ENGINE=\b.{2,}\b DEFAULT CHARSET=\S+/",'TYPE=MyISAM',$get_field);
								}
								$strfile .= str_replace('CREATE TABLE `'.C('DB_PREFIX'),'CREATE TABLE `#@_',$get_field.";\r\n");
							}
						}else{
							$page = 0;
							cookie('info_page',NULL);
							$p = cookie('info_step')?cookie('info_step'):1;
						}
					}else{
						$strfile .= "DROP TABLE IF EXISTS `".$tb."`;\r\n";
						$table_field = $sql->getField($table);		//获取表结构
						
						//替换数据表名
						$mysql = mysql_get_server_info();
						$get_field = preg_replace("/AUTO_INCREMENT=[0-9]+\s+/","",$table_field);
						if($arr_table['version']==4.1 && $mysql>4.1){
							$get_field = preg_replace("/ENGINE=\b.{2,}\b DEFAULT CHARSET=\S+/",'ENGINE=MyISAM DEFAULT CHARSET='.$arr_table['charset'],$get_field);
						}elseif($arr_table['version']==4.1 && $mysql<4.1){
							$get_field = preg_replace("TYPE=\b.{2,}\b",'ENGINE=MyISAM DEFAULT CHARSET='.$arr_table['charset'],$get_field);
						}elseif($arr_table['version']==4.0 && $mysql>4.1){
							$get_field = preg_replace("/ENGINE=\b.{2,}\b DEFAULT CHARSET=\S+/",'TYPE=MyISAM',$get_field);
						}
						$strfile .= str_replace('CREATE TABLE `'.C('DB_PREFIX'),'CREATE TABLE `#@_',$get_field.";\r\n");
							
						$info = $result->table($table)->select();
						cookie('info_page',NULL);
						cookie('info_step',NULL);
						$page = 0;
						$p = 1;
					}
				}
				
				if($info){
					while(true){
						$t = array_shift($info);
						$strfile .= $sql->getData($table,$t);
						if(strlen($strfile)>=$arr_table['filesize']*1024){
							$filename = $tb.'_'.str_pad($p,5,"0",STR_PAD_LEFT).'.bak';
							$fie_path = $bak_dir.'/'.$filename;	
							$path->putFile($fie_path,$strfile);
							$p++;
							$strfile = '';
							cookie('info_step',$p);
							$path->putFile(RUNTIME_PATH.'/backup.tmp',serialize($info));
							echo '<p>表“'.$table.'_'.str_pad(($p-1),5,"0",STR_PAD_LEFT).'”备份成功！</p>|1|'.$page; 
							exit;
						}else{
							if(count($info)){
								continue;
							}else{
								break;
							}
						}
					}
				}
				
				if($p==1){
					$filename = $tb.'.bak';
					$fie_path = $bak_dir.'/'.$filename;	
					$path->putFile($fie_path,$strfile);
					$path->delFile(RUNTIME_PATH.'/backup.tmp');
					echo '<p>表“'.$table.'”备份成功！</p>|0|0'; exit;
				}else{
					if($strfile){
						$filename = $tb.'_'.str_pad($p,5,"0",STR_PAD_LEFT).'.bak';
						$fie_path = $bak_dir.'/'.$filename;	
						$path->putFile($fie_path,$strfile);
					}
					$path->delFile(RUNTIME_PATH.'/backup.tmp');
					if($page>0){
						cookie('info_step',($p+1));
					}
					echo '<p>表“'.$table.'_'.str_pad($p,5,"0",STR_PAD_LEFT).'”备份成功！</p>|0|'.$page; exit;
				}
			}elseif($act=='re'){
				$str_table = $path->getFile(RUNTIME_PATH.'/database.tmp');
				$arr_table = unserialize($str_table);
				if($go==count($arr_table['table'])){
					$path->delFile(RUNTIME_PATH.'/database.tmp');
					echo '所有表已完成还原！|0|0'; exit;
				}
				$table = str_replace('#@_',C('DB_PREFIX'),$arr_table['table'][$go]);
				$tb = str_replace('.bak','',$table);
				$tablefile = $arr_table['path'].'/'.$arr_table['table'][$go];
				$info = $path->getFile($tablefile);
				$arr_info = explode(";\r\n",$info);
				$result = M();
				foreach($arr_info as $t){
					$t = preg_replace("/`#@_(.+)?`/iu",'`'.C('DB_PREFIX').'$1`',$t);
					$t = str_replace('&#59',';',$t);
					$char = C('CFG_CHARSET');
					if($char=='UTF-8'){
						$char = 'utf8';
					}else{
						$char = 'gb2312';
					}
					$t = preg_replace("/ENGINE=\b.{2,}\b DEFAULT CHARSET=\S+/",'ENGINE=MyISAM DEFAULT CHARSET='.$char,$t);
					$result->execute($t);
				}
				echo '<p>表“'.$tb.'”还原成功！</p>|0|0'; exit;
			}
		}else{
			$this->assign('act',$act);
			$this->assign('total',$total);
			$this->display();
		}	
	}
	
	/**
		* 删除备份数据
		*@examlpe 
	*/
	public function del(){
		$Public = A('Index','Public');
		$role = $Public->check('Backup',array('d'));
		if($role<0){
			echo $role; exit;
		}
		import('ORG.Net.FileSystem');
		$path = new FileSystem();
		$path->root = ITEM;
		$path->charset = C('CFG_CHARSET');
		
		//main
		$file = I('file');
		$realfile = CONF_PATH.'Backup/'.$file;
		if($file && file_exists($realfile)){
			$del = $path->delFile($realfile);
			if($del){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
		
		unset($Public,$path);
	}
	
	/**
		* 打包下载备份包
		*@param $file    文件路劲
		*@examlpe 
	*/
	public function downzip($file){
		import('ORG.Util.phpzip');
		$addzip = new phpzip();
		import('ORG.Net.FileSystem');
		$path = new FileSystem();
		$path->root = ITEM;
		$path->charset = C('CFG_CHARSET');
		load("@.download");
		
		//main
		$file = strval($file);
		$realpath = CONF_PATH.'Backup/'.$file;
		$bakfile = RUNTIME_PATH.'Temp/Zip/';
		if(!file_exists($bakfile)){
			$path->putDir($bakfile);
		}
		$zipname = 'Backup_'.$file.'.zip';
		$zippath = $bakfile.$zipname;
		$addzip->zip($realpath,$zippath);
		if(file_exists($zippath)){
			download($zippath);
			$path->delFile($zippath);
		}
		unset($addzip,$path);
	}
}