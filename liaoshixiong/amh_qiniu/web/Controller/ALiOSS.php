<?php



class ALiOSS extends AmysqlController

{

	public $indexs = null;

	public $ALiOSSs = null;

	public $notice = null;
	public $top_notice = null;


	// Model

	function AmysqlModelBase()

	{

		if($this -> indexs) return;

		$this -> _class('Functions');

		$this -> indexs = $this ->  _model('indexs');

		$this -> ALiOSSs = $this ->  _model('ALiOSSs');

	}





	function IndexAction()

	{

		$this -> ALiOSS_list();

	}


	// ALiOSS远程备份列表

	function ALiOSS_list()
	{
		$this -> title = 'ALiOSS 远程备份服务 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		$input_item = array('remote_status', 'remote_ip', 'remote_path', 'remote_user', 'remote_password');

		// 保存新配置
		if (isset($_POST['save']))
		{
			$save = true;
			foreach ($input_item as $val)
			{
				if(empty($_POST[$val]))
				{
					$this -> status = 'error';
					$this -> notice = '新增ALiOSS远程备份配置失败，请填写完整数据，*号为必填项。';
					$save = false;
					break;
				}
			}
			if($save)
			{
				$id = $this -> ALiOSSs -> ALiOSS_insert();
				if ($id)
				{
					$this -> status = 'success';
					$this -> notice = 'ID:' . $id . ' 新增ALiOSS远程备份配置成功。';
					$_POST = array();
				}
				else
				{
					$this -> status = 'error';
					$this -> notice = ' 新增ALiOSS远程备份配置失败。';
				}
			}
		}

		// 连接测试
		if (isset($_GET['check']))
		{
			$id = (int)$_GET['check'];
			$data = $this -> ALiOSSs -> get_ALiOSS($id);
			if (isset($data['remote_id']))
			{
				ini_set("max_execution_time", "15");
				$cmd = "php /usr/local/qiniu/check.php";
				$cmd = Functions::trim_cmd($cmd);
				$result = shell_exec($cmd);
				$result = trim(Functions::trim_result($result), "\n ");
				if($result == '1')
					echo '[OK]';
			}
			exit();
		}

		// 编辑远程配置
		if (isset($_GET['edit']))
		{
			$id = (int)$_GET['edit'];
			$_POST = $this -> ALiOSSs -> get_ALiOSS($id);
			if($_POST['remote_id'])
			{
				$this -> edit_remote = true;
			}
		}

		// 保存编辑远程配置
		if (isset($_POST['save_edit']))
		{
			$id = $_POST['remote_id'] = (int)$_POST['save_edit'];
			$save = true;
			foreach ($input_item as $val)
			{
				if(empty($_POST[$val]) && $val != 'remote_password')
				{
					$this -> status = 'error';
					$this -> notice = 'ID:' . $id . ' 编辑ALiOSS远程备份配置失败。*号为必填项。';
					$save = false;
					$this -> edit_remote = true;
					break;
				}
			}
			if ($save)
			{
				$result = $this -> ALiOSSs -> ALiOSS_update();
				if ($result)
				{
					$this -> status = 'success';
					$this -> notice = 'ID:' . $id . ' 编辑ALiOSS远程备份配置成功。';
					$_POST = array();
				}
				else
				{
					$this -> status = 'error';
					$this -> notice = 'ID:' . $id . ' 编辑ALiOSS远程备份配置失败。';
					$this -> edit_remote = true;
				}
			}
		}

		// 删除远程配置
		if (isset($_GET['del']))
		{
			$id = (int)$_GET['del'];
			if(!empty($id))
			{
				$result = $this -> ALiOSSs -> ALiOSS_del($id);
				if ($result)
				{
					$this -> status = 'success';
					$this -> top_notice = 'ID:' . $id . ' 删除ALiOSS远程备份配置成功。';
				}
				else
				{
					$this -> status = 'error';
					$this -> top_notice = 'ID:' . $id . ' 删除ALiOSS远程备份配置失败。';
				}
			}
		}

		$this -> ALiOSS_list_data = $this -> ALiOSSs -> get_ALiOSS_list();
		$this -> indexs -> log_insert($this -> notice);
		$this -> _view('ALiOSS_list');
	}
	
	// ******************************************************
	// Bucket列表
	function Bucket_list()
	{
		$this -> title = 'Bucket存储空间列表 - ALiOSS - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		// 删除Bucket
		if (isset($_GET['delete']))
		{
			$delete = $_GET['delete'];
			$Bucket_name = $_GET['Bucket_name'];
			$result = $this -> ALiOSSs -> Bucket_delete($delete);
			if (strpos($result, '<Error>') === false)
			{
				$this -> status = 'success';
				$this -> top_notice = 'Bucket存储空间删除成功：' . $Bucket_name;
			}
			else
			{
			    $this -> status = 'error';
				$this -> top_notice = 'Bucket存储空间删除失败：' . $Bucket_name;
			}
		}

		$this -> Bucket_list_data = $this -> ALiOSSs -> get_Bucket_list();
		$this -> indexs -> log_insert($this -> top_notice);
		$this -> _view('Bucket_list');
	}
	
	// Object列表
	function Object_list()
	{
		$this -> title = 'Object列表 - ALiOSS - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		// 本地下载
		if (isset($_GET['download']))
		{
			$remote_id = (int)$_GET['remote_id'];
			$download = $_GET['download'];
			$url = $this -> ALiOSSs -> get_Object_url($remote_id, $download);
			if (!empty($url))
			{
				header('location: ' . $url);
				exit();
			}
			$this -> Bucket_list();
			exit();
		}
		// 下载到服务器备份目录
		if (isset($_GET['download_local']))
		{
			$remote_id = (int)$_GET['remote_id'];
			$download = $_GET['download_local'];
			$this -> ALiOSSs -> get_Object_download($remote_id, $download);
			$this -> indexs -> log_insert('AliOSS 下载到服务器备份目录: ' . $download);
			exit();
		}

		// 删除文件
		if (isset($_GET['delete']))
		{
			$remote_id = (int)$_GET['remote_id'];
			$delete = $_GET['delete'];
			$this -> ALiOSSs -> get_Object_delete($remote_id, $delete);
			$this -> indexs -> log_insert('AliOSS 删除文件: ' . $delete);
			exit();
		}

		// 本地amh备份列表
		if (isset($_GET['amh_backup_list']))
		{
			$amh_backup_list = $this -> ALiOSSs -> get_amh_backup_list();
			$data = array();
			foreach ($amh_backup_list as $key=>$val)
			{
				$row = $this -> ALiOSSs -> Grawlistline($val);
				if(substr($row['dirfilename'], -4) == '.amh') 
				{
					$row['md5'] = md5($row['dirfilename']);
					$data[] = $row;
				}
			}
			echo json_encode($data);
			exit();
		}


		$remote_id = (int)$_GET['remote_id'];
		$alioss_path = $_GET['path'];
		$this -> Object_list_data = $this -> ALiOSSs -> get_Object_list($remote_id, $alioss_path);
		$this -> indexs -> log_insert($this -> notice);
		$this -> _view('Object_list');
	}




}



?>