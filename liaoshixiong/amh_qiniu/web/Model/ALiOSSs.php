<?php

class ALiOSSs extends AmysqlModel
{
	// 取得ALiOSS远程备份列表
	function get_ALiOSS_list()
	{
		$sql = "SELECT * FROM amh_backup_remote WHERE remote_type = 'AliOSS' ORDER BY remote_id ASC ";
		Return $this -> _all($sql);	
	}

	// 新增ALiOSS远程备份设置
	function ALiOSS_insert()
	{
		$_POST['remote_type'] = 'ALiOSS';
		$_POST['remote_pass_type'] = '2';
		$data_name = array('remote_type', 'remote_status', 'remote_ip', 'remote_path', 'remote_user', 'remote_pass_type', 'remote_password', 'remote_comment');
		foreach ($data_name as $val)
			$insert_data[$val] = $_POST[$val];
		Return $this -> _insert('amh_backup_remote', $insert_data);
	}

	// 取得ALiOSS远程备份
	function get_ALiOSS($remote_id)
	{
		$sql = "SELECT * FROM amh_backup_remote WHERE remote_id = '$remote_id' AND remote_type = 'ALiOSS' ";
		Return $this -> _row($sql);
	}

	//获取第一个配置数据
	function get_First_ALiOss(){
		$sql = "SELECT * FROM amh_backup_remote where remote_type = 'ALiOSS' limit 1";
		Return $this -> _row($sql);
	}

	// 更新保存ALiOSS配置
	function ALiOSS_update()
	{
		$data_name = array('remote_status', 'remote_ip', 'remote_path', 'remote_user', 'remote_password', 'remote_comment');
		foreach ($data_name as $val)
		{
			if($val != 'remote_password' || !empty($_POST['remote_password']))
				$update_data[$val] = $_POST[$val];
		}
		Return $this -> _update('amh_backup_remote', $update_data,  " WHERE remote_id = '$_POST[remote_id]' ");
	}

	// 删除ALiOSS配置
	function ALiOSS_del($remote_id)
	{
		$sql = "DELETE FROM amh_backup_remote WHERE remote_id = '$remote_id' AND remote_type = 'ALiOSS'";
		$this -> _query($sql);
		Return $this -> Affected;
	}

	// ******************************************************

	// 取得Bucket列表
	function get_Bucket_list()
	{
		$ALiOSS_list = $this -> get_ALiOSS_list();

		$acl_cn = array('private' => '私有读写', 'public-read' => '公共读', 'public-read-write' => '公共读写');
		$Bucket_list = array();
		$exist_id_key = array();
		foreach ($ALiOSS_list as $key=>$val)
		{
			$Bucket_list[$val['remote_path']]['remote_id'] = $val['remote_id'];		// 同KeyID只记录ALiOSS-ID
			if(!in_array($val['remote_user'], $exist_id_key))						// 同KeyID不重复查询
			{
				$exist_id_key[] = $val['remote_user'];
				$cmd = "amh module ALiOSS-1.1 admin gs,{$val['remote_id']}";
				$cmd = Functions::trim_cmd($cmd);
				$result = shell_exec($cmd);
				$result = trim(Functions::trim_result($result), "\n ");
				preg_match_all("/(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2})\s+([a-z0-9\-]{3,})/", $result, $row);
				if (is_array($row))
				{
					foreach ($row[2] as $k=>$v)
					{
						$Bucket_list[$v]['Bucket_time'] = $row[1][$key];
						$Bucket_list[$v]['Bucket_name'] = $v;
						$Bucket_list[$v]['remote_user'] = $val['remote_user'];
					}
				}
			}
		}

		// 查读写权限
		foreach ($Bucket_list as $key=>$val)
		{
			// 匹配得到Bucket空间与存在ALiOSS设置连接的Bucket
			if(isset($val['Bucket_name']) && isset($val['remote_id']))
			{
				$cmd = "amh module ALiOSS-1.1 admin gs-acl,{$val['remote_id']}";
				$cmd = Functions::trim_cmd($cmd);
				$result = shell_exec($cmd);
				$result = trim(Functions::trim_result($result), "\n ");
				preg_match("/(private|public\-read|public\-read\-write)/", $result, $row);
				$Bucket_list[$key]['acl'] = isset($acl_cn[$row[1]]) ? $acl_cn[$row[1]] : '/';
			}
			else
			{
			    unset($Bucket_list[$key]);
			}
		}
		Return $Bucket_list;
	}

	// 删除ALiOSS文件
	function Bucket_delete($remote_id)
	{
		$Object = $this -> get_ALiOSS($remote_id);
		if (isset($Object['remote_path']))
		{
			$cmd = "amh module ALiOSS-1.1 admin rm-all,{$remote_id}";
			$cmd = Functions::trim_cmd($cmd);
			Return shell_exec($cmd);
		}
	}



	// 取得Object列表
	function get_Object_list($remote_id, $alioss_path)
	{
		$cmd = "amh module ALiOSS-1.1 admin ls,{$remote_id},{$alioss_path}";
		$cmd = Functions::trim_cmd($cmd);
		$result = shell_exec($cmd);
		$result = trim(Functions::trim_result($result), "\n ");
		preg_match_all("/(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2})\s+([0-9A-Z]+)\s+oss\:\/\/(.*)\n/U", $result, $row);
		if (is_array($row))
		{
			foreach ($row[3] as $k=>$v)
			{
				$v = trim($v);
				$pathinfo = pathinfo($v);
				$object_type = substr(trim($v), -1) == '/' ? 'dir' : 'file';
				$Object_list[$v]['path'] = 'oss://' . $v;
				$Object_list[$v]['time'] = trim($row[1][$k]);
				$Object_list[$v]['size'] = $object_type == 'dir' ? '-' : trim($row[2][$k]);
				if(strpos($Object_list[$v]['size'], 'M'))
					$Object_list[$v]['size_kb'] = (int)$Object_list[$v]['size'] * 1024*1024;
				elseif(strpos($Object_list[$v]['size'], 'G'))
					$Object_list[$v]['size_kb'] = (int)$Object_list[$v]['size'] * 1024*1024*1024;
				elseif(strpos($Object_list[$v]['size'], 'KB'))
					$Object_list[$v]['size_kb'] = (int)$Object_list[$v]['size'] * 1024;
				elseif(strpos($Object_list[$v]['size'], 'B'))
					$Object_list[$v]['size_kb'] = (int)$Object_list[$v]['size'];
				$Object_list[$v]['basename'] = $pathinfo['basename'];
				$Object_list[$v]['extension'] = $object_type == 'dir' ? 'Dir' : $pathinfo['extension'];
				$Object_list[$v]['object_type'] = $object_type;
			}
		}
		ksort($Object_list);
		/*
		ksort($Object_list['dir']);
		ksort($Object_list['file']);
		foreach ($Object_list['dir'] as $key=>$val)
			$data[] = $val;
		foreach ($Object_list['file'] as $key=>$val)
			$data[] = $val;
		*/
		Return $Object_list;
	}

	// 取得object的地址
	function get_Object_url($remote_id, $download)
	{
		$Object = $this -> get_ALiOSS($remote_id);
		if (isset($Object['remote_path']))
		{
			$Bucket_name = 'oss://' . $Object['remote_path'] . '/';
			$alioss_file = str_replace($Bucket_name, '', $download);
			$cmd = "amh module ALiOSS-1.1 admin url,{$remote_id},{$alioss_file}";
			$cmd = Functions::trim_cmd($cmd);
			$result = shell_exec($cmd);
			preg_match("/\n(.*oss\.aliyuncs\.com.*)\n$/U", $result, $row);
			Return $row[1];
		}
	}

	// 下载到amh备份目录(后台执行)
	function get_Object_download($remote_id, $download)
	{
		$Object = $this -> get_ALiOSS($remote_id);
		if (isset($Object['remote_path']))
		{
			$Bucket_name = 'oss://' . $Object['remote_path'] . '/';
			$alioss_file = str_replace($Bucket_name, '', $download);
			$cmd = "amh module ALiOSS-1.1 admin get,{$remote_id},{$alioss_file}";
			$cmd = Functions::trim_cmd($cmd);
			$cmd .= ' >/dev/null &';
			shell_exec($cmd);
		}
	}

	// 删除ALiOSS文件
	function get_Object_delete($remote_id, $delete)
	{
		$Object = $this -> get_ALiOSS($remote_id);
		if (isset($Object['remote_path']))
		{
			$Bucket_name = 'oss://' . $Object['remote_path'] . '/';
			$alioss_file = str_replace($Bucket_name, '', $delete);
			$cmd = "amh module ALiOSS-1.1 admin rm,{$remote_id},{$alioss_file}";
			$cmd = Functions::trim_cmd($cmd);
			shell_exec($cmd);
		}
	}

	// 本地备份amh文件列表
	function get_amh_backup_list()
	{
		$cmd = 'amh ls_backup';
		$cmd = Functions::trim_cmd($cmd);
		Return explode("\n", shell_exec($cmd));
	}

	// 分析文件列表
	function Grawlistline($rawlistline)
	{
		if (preg_match("/([-dl])([rwxsStT-]{9})[ ]+([0-9]+)[ ]+([^ ]+)[ ]+(.+)[ ]+([0-9]+)[ ]+([a-zA-Z]+[ ]+[0-9]+)[ ]+([0-9:]+)[ ]+(.*)/", $rawlistline, $regs) == true) 
		{
			$listline["scanrule"]         = 'rule-1';
			$listline["dirorfile"]        = $regs[1];		
			$listline["dirfilename"]      = $regs[9];		
			$listline["size"]             = $regs[6];		
			$listline["owner"]            = $regs[4];		
			$listline["group"]            = $regs[5];		
			$listline["permissions"]      = $regs[2];		
			$listline["mtime"]            = "$regs[7] $regs[8]";	
		}
		elseif (preg_match("/([-dl])([rwxsStT-]{9})[ ]+(.*)[ ]+([a-zA-Z0-9 ]+)[ ]+([0-9:]+)[ ]+(.*)/", $rawlistline, $regs) == true) 
		{
			$listline["scanrule"]         = 'rule-2';
			$listline["dirorfile"]        = $regs[1];		
			$listline["dirfilename"]      = $regs[6];		
			$listline["size"]             = $regs[3];		
			$listline["permissions"]      = $regs[2];		
			$listline["mtime"]            = "$regs[4] $regs[5]";	
		}
		elseif (preg_match("/([0-9\\/-]+)[ ]+([0-9:AMP]+)[ ]+([0-9]*|<DIR>)[ ]+(.*)/", $rawlistline, $regs) == true) 
		{
			$listline["scanrule"]         = 'rule-3.1';
			$listline["size"] = ($regs[3] == "<DIR>") ? '' : $regs[3]; 
			$listline["dirfilename"] = $regs[4];		
			$listline["owner"]            = '';			
			$listline["group"]            = '';			
			$listline["permissions"]      = '';			
			$listline["mtime"]            = "$regs[1] $regs[2]";	
			$listline["dirorfile"] = ($listline["size"] != '') ? '-' : 'd';
		}
		elseif (preg_match("/([-]|[d])[ ]+(.{10})[ ]+([^ ]+)[ ]+([0-9]*)[ ]+([a-zA-Z]*[ ]+[0-9]*)[ ]+([0-9:]*)[ ]+(.*)/", $rawlistline, $regs) == true) 
		{
			$listline["scanrule"]         = 'rule-3.2';
			$listline["dirorfile"]        = $regs[1];		
			$listline["dirfilename"]      = $regs[7];		
			$listline["size"]             = $regs[4];		
			$listline["owner"]            = $regs[3];		
			$listline["group"]            = '';			
			$listline["permissions"]      = $regs[2];		
			$listline["mtime"]            = "$regs[5] $regs6";	
		}
		elseif (preg_match("/([a-zA-Z0-9_-]+)[ ]+([0-9]+)[ ]+([0-9\\/-]+)[ ]+([0-9:]+)[ ]+([a-zA-Z0-9_ -\*]+)[ \\/]+([^\\/]+)/", $rawlistline, $regs) == true) 
		{
			if ($regs[5] != "*STMF") $directory_or_file = 'd';
			if ($regs[5] == "*STMF") $directory_or_file = '-';
			$listline["scanrule"]         = 'rule-3.3';
			$listline["dirorfile"]        = $directory_or_file;
			$listline["dirfilename"]      = $regs[6];		
			$listline["size"]             = $regs[2];		
			$listline["owner"]            = $regs[1];		
			$listline["group"]            = '';			
			$listline["permissions"]      = '';			
			$listline["mtime"]            = "$regs[3] $regs[4]";	
		}
		elseif (preg_match("/([-dl])([rwxsStT-]{9})[ ]+([0-9]+)[ ]+([a-zA-Z0-9]+)[ ]+([a-zA-Z0-9]+)[ ]+([0-9]+)[ ]+([a-zA-Z]+[ ]+[0-9]+)[ ]+([0-9:]+)[ ](.*)/", $rawlistline, $regs) == true) 
		{
			$listline["scanrule"]         = 'rule-3.4';
			$listline["dirorfile"]        = $regs[1];        
			$listline["dirfilename"]      = $regs[9];        
			$listline["size"]             = $regs[6];        
			$listline["owner"]            = $regs[4];        
			$listline["group"]            = $regs[5];        
			$listline["permissions"]      = $regs[2];        
			$listline["mtime"]            = "$regs[7] $regs[8]";    
		}
		else 
		{
			$listline["scanrule"]         = 'rule-4';
			$listline["dirorfile"]        = 'u';
			$listline["dirfilename"]      = $rawlistline;
		}
		
		Return $listline;
	}

}

?>