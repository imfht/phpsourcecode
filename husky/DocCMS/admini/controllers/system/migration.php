<?php
checkme(10);
function index(){}
/*打包文件*/
function packageDatabase()
{
	usleep(500000);
	$tempBackupPath=ABSPATH.'/temp/temp';
	$dbxmls = listDirTree($tempBackupPath);
	if(!empty($dbxmls))
	{
		$backupfile=ABSPATH.'/temp/data/'.DB_DBNAME.'_database.xml';
		string2file($xmlString,$backupfile);
		if(is_file($backupfile))
		{
			$tmpdbstring = '';
			foreach($dbxmls as $k=>$v)
			{
				if(extendName($v)=='xml' && $v!=DB_DBNAME.'_database.xml')
				{
					if(filesize($tempBackupPath.'/'.$v)>0)
					{
						$tmpxml = file2string($tempBackupPath.'/'.$v);
						@unlink($tempBackupPath.'/'.$v);
						$tmpdbstring .= $tmpxml;
					}elseif(filesize($tempBackupPath.'/'.$v)==0){
						@unlink($tempBackupPath.'/'.$v);
					}else{
						
					}
				}
			}
			$tmpdbstring = str_replace('<?xml version="1.0" encoding="utf-8"?><root>','<tables>',$tmpdbstring);
			$tmpdbstring = str_replace('</root>','</tables>',$tmpdbstring);
			$tmpdbstring = '<?xml version="1.0" encoding="utf-8"?><root>'.$tmpdbstring.'</root>';
			string2file($tmpdbstring,$backupfile);
		}
	}
	die('<script>alert("数据库 打包成功");setTab(1,0)</script>');
	//die('数据库 打包成功..<br />');
}
function export()//导出各表的数据
{
	global $db,$request;
	usleep(200000);
	require(ABSPATH.'config/doc-config-tables.php');
	$curtable=$tablesArr[$request['num']];
	unset($tablesArr);
	if(!empty($curtable))
	{
		if($curtable['name']=='list_content'|| $curtable['name']=='solutions_content'){
			$sql = "SELECT * FROM ".TB_PREFIX.$curtable['name'];
		}else{
			$sql = "SELECT * FROM ".TB_PREFIX.$curtable['name']." ORDER BY id ASC";
		}
	}
	else
	{
		die($request['num'].'表不存在..<br />');
	}
	$menuResults = $db->get_results($sql);
	constructXML($menuResults,$curtable['name']);
	die($curtable['name'].'表导出数据完成..<br />');
}
function constructXML($resultsArray=array(),$prefix)//生成xml表数据
{
	global $xmlString;
	if(!empty($resultsArray))
	{
		$xmlString = '<?xml version="1.0" encoding="utf-8"?><root><'.$prefix.'>';
		foreach($resultsArray as $o)
		{
			$xmlString .= '<item>';
			foreach($o as $k=>$v)
			{
				$xmlString .= '<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
			}
			$xmlString .= '</item>';
		}
		$xmlString .= '</'.$prefix.'></root>';
	}
	$tempBackupPath=ABSPATH.'/temp/temp';
	if(is_dir($tempBackupPath)) 
	string2file($xmlString,$tempBackupPath.'/'.$prefix.'.xml');
}
/*打包文件 
 */
function manage()
{	
}
/*下载文件*/
function downloadXMLWebData()//下载数据
{	
	global $request;
	$filename=$request['filename'];
	$ext='.xml';
	if(!valiextend($filename,$ext))
	{
		exit($filename.'不是合法'.$ext.'文件！');
	}
	_download($filename,$ext,'data');
}
function _download($filename='',$ext='',$dir='')//提供下载
{
	$backupfile=ABSPATH.'/temp/'.$dir.'/'.$filename;
	if(is_file($backupfile))
	{
		header('Content-type: application/'.$ext);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		readfile($backupfile);
		exit;
	}
	else
	{
		exit($filename.'文件 不存在！');
	}
}
/*下载文件 
 */
/*删除文件*/
function deleteXMLWebData()//删除单个数据文件
{
	global $request;
	$filename=$request['filename'];
	$ext='.xml';
	if(!valiextend($filename,$ext))
	{
		exit($filename.'不是合法'.$ext.'文件！');
	}
	_delete($filename,'data');
	redirect($_SERVER['HTTP_REFERER']);
}
function deleteXMLWebDatas()//删除多个数据文件
{
	global $request;
	$filenames=$request['filenames'];
	$ext='.xml';
	if(!empty($filenames))
	{
		foreach($filenames as $filename)
		{
			if(!valiextend($filename,$ext))
			{
				exit($filename.'不是合法'.$ext.'文件！');
			}
			_delete($filename,'data');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	else
	{
		exit('请选择删除的xml数据文件');
	}
}
function _delete($filename='',$dir='')//提供删除
{
	$backupfile=ABSPATH.'/temp/'.$dir.'/'.$filename;
	if(is_file($backupfile))
	{
		@unlink($backupfile);
	}
	else
	{
		exit($filename.'文件 不存在！');
	}
}
/*删除文件
 */
/*上传文件*/
function uploadWebData()//上传数据
{
	global $request;
	$filename=$_FILES['uploadfile']['name'];
	$maxfilesize='1000000000000';
	
	$ext='.xml';
	$dir='data';
	if(!valiextend($filename,$ext))
	{
		exit($filename.'不是合法'.$ext.'文件！');
	}
	$curFile=ABSPATH.'/temp/'.$dir.'/'.$filename;
	if(is_file($curFile))
	{
		exit('存在同名文件，请更换名称');
	}
	_upload($curFile,'uploadfile',$maxfilesize);
}
function _upload($curFile='',$uploadfile='uploadfile',$maxfilesize=10)//提供上传
{
	if(!empty($_FILES[$uploadfile]))
	{
		echo $_FILES[$uploadfile]['size'];
		echo $maxfilesize;
		if($_FILES[$uploadfile]['size']>0 && $_FILES[$uploadfile]['size']<$maxfilesize)
		{
			if(!move_uploaded_file($_FILES[$uploadfile]['tmp_name'],$curFile))
			{
				exit("<script>alert('文件上传系统操作错误。');window.history.go(-1);</script>");	
			}
			else
			{
				exit("<script>alert('文件上传成功!');window.history.go(-1);</script>");
			}
		}
		elseif($_FILES[$uploadfile]['size']==0)
		{
			exit("<script>alert('请选择上传的文件!');window.history.go(-1);</script>");
		}
		else
		{
			exit("<script>alert('文件上传失败!');window.history.go(-1);</script>");
		}
	}
	else
	{
		exit("<script>alert('请选择上传文件!');window.history.go(-1);</script>");
	}
}
/*上传文件
 */
function importWebData()//导入数据
{
	require 'migration/importWebData.php';
}
/* 功能函数*/
function listDirTree($dirName=null)//递归目录树
{
	if(empty($dirName))
	exit("IBFileSystem: directory is empty.");
	if(is_dir($dirName))
	{
		if($dh=opendir($dirName))
		{
			$tree=array();
			while(($file=readdir($dh))!==false)
			{
				if($file!="."&&$file!="..")
				{
					$filePath=$dirName."/".$file;		
					$filename=$file;
					if(is_dir($filePath)) //为目录,递归
					{
						$tree[$file]=listDirTree($filePath);
						//$tree[$file]=$filename;
					}
					else //为文件,添加到当前数组
					{						
						//$tree[]=$file;
						$tree[]=$filename;
					}
				}
			}
			closedir($dh); 
		}
		else
		{
			exit("IBFileSystem: can not open directory $dirName.");
		}
		return $tree;
	}
	else
	{
		exit("IBFileSystem: $dirName is not a directory.");
	}
}
function extendName($file_name)
{
	$extend =explode(".", $file_name);
	$va=count($extend)-1;
	return $extend[$va];
}
function valiextend($filename,$ext){//验证指定的扩展名
	$extlen=strlen($ext);
	if($ext!=strtolower(substr($filename,-$extlen,$extlen))){
		return false; 
	}
	else{
		return true;
	}
}
function getBackupList(){
	global $request;
	$ind= intval($request['i']);
	$cfg=array( '0'=>array('/data','/*.xml'));
	if(!isset($cfg[$ind])) exit('forbidden');
	$files = glob( ABSPATH.'/temp'.$cfg[$ind][0].$cfg[$ind][1] );
	if(is_array($files))
	{
		$info = $infos = array();
		foreach($files as $id=>$file)
		{
			$info['id'] = $id+1;
			$info['filename'] = basename($file);
			$info['filesize'] = round(filesize($file)/(1024), 2);
			$info['maketime'] = date('Y-m-d H:i:s', filemtime($file));
			$info['bgcolor'] = $info['bgcolor'] == '#F1F3F5' ? '#E4EDF9' : '#F1F3F5';
			$infos[] = $info;
		}
	}else{
		$infos=array();
	}
	
	if(function_exists('json_encode'))
	{
		exit(json_encode($infos));
	}else{
		require_once ABSPATH.'/inc/class.json.php';
		$json=new Services_JSON();
		exit($json->encode($infos));
	}
}
?>