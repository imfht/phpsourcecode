<?php
checkme(10);
require(ABSPATH.'/inc/class.zip.php');
require_once(ABSPATH.'/inc/class.upload.php');
function index()
{
	global $model_list,$db,$request;

	$sb = new sqlbuilder('mdt','SELECT * FROM `'.TB_PREFIX.'models_reg`','id',$db,20);
	$model_list = new DataTable($sb,'扩展模块列表');
	$model_list->add_col('编号','id','db',40,'"$rs[id]"');
	$model_list->add_col('名称','model_name','db',0,'"$rs[model_name]"');
	$model_list->add_col('类型','type','db',0,'"$rs[type]"');
	$model_list->add_col('模块简介','summary','db',550,'"$rs[summary]"');
	$model_list->add_col('版本','version','db',80,'"$rs[version]"');
	$model_list->add_col('操作','edit','text',140,'"<a href=\"./index.php?m=system&s=managemodel&a=unstall_model&model_type=$rs[type]\" onclick=\"return confirm(\'您确认要删除本模块?一旦删除，将不可恢复。\');\">[删除]</a>"');
}
function upload_model()
{
	//把模版先暂时上传在系统根目录的TEMP文件夹里，解决safe_mode On时无法上传在环境文件夹下
	//suny.2008.01.16
	$upload = new Upload(10000,'/temp/');
	$fileName = $upload->SaveFile('upfile');
	if(is_file(ABSPATH.'/temp/'.$fileName))
	{
		del_dir(ABSPATH.UPLOADPATH.'temp/');
		mkdirs(ABSPATH.UPLOADPATH.'temp/');
		if(unzip(ABSPATH.UPLOADPATH.'temp/',ABSPATH.'/temp/'.$fileName,ABSPATH.'/temp/'.$fileName)==1)
		{
			$doc = get_config_xmldoc('config');
			exec_config($doc);
			$doc = get_config_xmldoc('install');
			exec_install($doc);
	
			redirect('?m=system&s=managemodel');
		}
	}
}
function unstall_model()
{
	global $db,$request;
	$uninstall = $db->get_row('SELECT * FROM `'.TB_PREFIX."models_reg` WHERE type='$request[model_type]' AND readonly=0");
	if($uninstall)	
	{
		$doc = get_config_xmldoc('install');
		$doc = new DOMDocument(null,'utf-8');
		$doc->loadXML(stripslashes($uninstall->unstall));
		exec_install($doc);
	}
	else
	{
		echo "<script>alert('此模块不能删除!')</script>";
	}
	redirect('?m=system&s=managemodel');
}
//-----------------------------------------------------功能函数---------------------------------------//
function del_dir($dir) {
	if(!is_dir($dir))
	{
	@unlink($dir);
	return true;
	}
	$dh=opendir($dir);
	while ($file=readdir($dh)) {
		if($file!="." && $file!="..") {
			$fullpath=$dir."/".$file;
			if(!is_dir($fullpath)) {
				@unlink($fullpath);
			} else {
				del_dir($fullpath);
			}
		}
	}

	closedir($dh);

	if(rmdir($dir)) {
		return true;
	} else {
		return false;
	}
}

function get_directory($dirstr)
{
	$skinsArr = array();
	$handle=opendir($dirstr);  //这里输入其它路径
	while (false !== ($file = readdir($handle)))
	{
		if ($file != "." && $file != "..") {
			if(is_dir($dirstr.$file))
			{
				$skinsArr[] = $file;
			}
		}
	}
	closedir($handle);
	return $skinsArr;
}
function get_config_xmldoc($filename)
{
	$xmlFilePath= ABSPATH.UPLOADPATH.'temp/'.$filename.'.xml';
		if(is_file($xmlFilePath))
		{
			$doc = new DOMDocument(null,'utf-8');
			$doc->load($xmlFilePath);
			return $doc;
		}
}

function del_file($dir) {

	$dh=opendir($dir);

	while ($file=readdir($dh)) {
		if($file!="." && $file!="..") {
			$fullpath=$dir."/".$file;
			if(!is_dir($fullpath)) {
				@unlink($fullpath);
			} else {
				delFile($fullpath);
			}
		}
	}
	closedir($dh);
}
//--------------------------------安装模块时使用-----------------------------------
function exec_install($doc)
{
	$install = $doc->getElementsByTagName('cmd');
	foreach ($install as $xml)
	{
		switch($xml->getAttribute('type')){
			case 'copy':
				exec_copy($xml);
				break;
			case 'sql':
				exec_sql($xml);
				break;
			case 'unlink':
				exec_ulink($xml);
				break;
		}
	}
}
function exec_copy($xml)
{
	$params=get_params($xml);
	$params['source'] = ABSPATH.UPLOADPATH.'temp/'.$params['source'];
	$params['destination'] = ABSPATH.'/'.$params['destination'];
	mkdirs($params['destination']);
	copy($params['source'],$params['destination']);
}
function exec_ulink($xml)
{
	$params=get_params($xml);
	$params['file'] = ABSPATH.'/'.$params['file'];
	del_dir($params['file']);
}
function exec_sql($xml)
{
	global $db;
	$params=get_params($xml);
	$db->query($params['sql']);
}
function get_params($xml)
{
	$params=array();
	foreach ($xml->getElementsByTagName('param') as $item)
	{
		$params[$item->getAttribute('name')]=replace_words($item->nodeValue);
	}
	return $params;
}

//安装配置文件
function exec_config($doc)
{
	$config=array();
	$config['model_name']	=$doc->getElementsByTagName('model_name')->item(0)->nodeValue;
	$config['type']			=$doc->getElementsByTagName('type')->item(0)->nodeValue;
	$config['summary']		=$doc->getElementsByTagName('summary')->item(0)->nodeValue;
	$config['version']		=$doc->getElementsByTagName('version')->item(0)->nodeValue;
	$config['sql']			=$doc->getElementsByTagName('sql')->item(0)->nodeValue;
	$config['config']		=addslashes(file2string(ABSPATH.UPLOADPATH.'/temp/config.xml'));
	$config['install']		=addslashes(file2string(ABSPATH.UPLOADPATH.'/temp/install.xml'));
	$config['unstall']		=addslashes(file2string(ABSPATH.UPLOADPATH.'/temp/uninstall.xml'));
	if(model_exists($config['type']))die('您安装的模块已存在，请先卸载。');
	$models_reg1 = new models_reg();
	$models_reg1->addnew($config);
	$models_reg1->save();
}
function replace_words($str)
{
	$str = str_replace('{TB_PREFIX}',TB_PREFIX,$str);
	//$str = str_replace('{TB_PREFIX}',TB_PREFIX,$str);
	return $str;
}
function model_exists($model_type)
{
	global $db;
	$sql = "SELECT count(*) FROM `".TB_PREFIX."models_reg` WHERE `type`='$model_type'";
	if($db->get_var($sql))
	return true;
	else 
	return false;
}
?>
