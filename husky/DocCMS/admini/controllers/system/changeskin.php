<?php
require(ABSPATH.'/inc/class.zip.php');
require_once(ABSPATH.'/inc/class.upload.php');
checkme(10);
function index()
{	
}
function upload_template()
{
	global $error;
	//unzip(ABSPATH.'/'.SKINROOT,$_FILES["upfile"][tmp_name],$_FILES["upfile"][name])==1;

	//把模版先暂时上传在系统根目录的TEMP文件夹里，解决safe_mode On时无法上传在环境文件夹下
	//suny.2008.10.16
	$upload = new Upload(10000,'/temp/');
	$fileName = $upload->SaveFile('upfile');
	if(is_file(ABSPATH.'/temp/'.$fileName))
	{
		if(unzip(ABSPATH.'/'.SKINROOT,ABSPATH.'/temp/'.$fileName,ABSPATH.'/temp/'.$fileName)==1)
		echo '<script language="javascript">alert("安装成功!");history.back(1);</script>';
		elseif(unzip(ABSPATH.'/'.SKINROOT,ABSPATH.'/temp/'.$fileName,ABSPATH.'/temp/'.$fileName)==0)
		echo '<script language="javascript">alert("安装失败!");history.back(1);</script>';
		else
		echo '<script language="javascript">alert("此文件不是ZIP格式!");history.back(1);</script>';
	}
	else
	{
		echo '<script language="javascript">alert("文件上传失败!");history.back(1);</script>';
	}
	redirect('?m=system&s=changeskin');
}

function changeSkin()
{
	global $request;
	change_skin_by_name($request['skin']);
	redirect('?m=system&s=changeskin');
}
//-------------------------------------修改模板-----------------------------------------------------//
/**
 * 修改模板
 */
function editSkin()
{
	global $request,$skinTreeHtml;
	$skin = filter_submitname( $request['skin'] );
	$skinTreeHtml= treeArray(listDirTree(get_abs_skin_root()), '/'.$skin);
}
/*遍历下面要用到的多维目录数组*/
function treeArray($arySrc,$selected='',$is_all=true)
{
	global $request;
	$treeHtml ='';
	if(is_array($arySrc))
	{
		//print_r($arySrc);
		ksort($arySrc);
		$keditFileTypes  = array('php','shtml','html','htm','xml','log','txt','js','css'); //可编辑
		$uploadFileTypes = array('jpg','gif','jpeg','png','swf','fla','flv','avi','mpg','mpeg','ico');//可上传
		$fobbdnFileTypes = array('php2','php3','php4','php5','phtml','pwml','inc','db','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','com','dll','vbs','reg','cgi','htaccess','asis') ;//禁止显示
		$treeHtml   .= "	<dl>\n";
		$className=time();
		foreach($arySrc as $kk=>$vv)
		{
			if($kk=="dir"){
				asort($vv);
				foreach($vv as $k=>$v){
					$k=preg_replace('/[\/]{2,}/', '/', $k);									    //修正路径格式
					$hasClass = $k != $selected ? $className : $className.' seleted';
					/*id重复问题  解决*/
					$curId = md5($k.$className);
					$childrenId = 'next_'.$curId;
					if($is_all)
					{
						$treeHtml .= '<dt><span><img src="./images/tree/ico_folder.gif" ></span><a name="tree" type="dir" href="'.$k.'"  title="下一级" id="'.$curId.'" class="'.$hasClass.'" child="'.$childrenId.'">'.$v.'</a><dd id=\''.$childrenId.'\' class="dir"></dd></dt>'."\n";
					}else{
						$treeHtml .= '<dt>+<a name="tree" type="dirs" href="'.$k.'"  title="下一级" id="'.$curId.'" class="'.$hasClass.'" child="'.$childrenId.'">'.$v.'</a><dd id=\''.$childrenId.'\' class="dir"></dd></dt>'."\n";
					}
				}
			}
			if($kk=="file" &&  $is_all ){
				asort($vv);
				//print_r($vv);
				foreach($vv as $k=>$v){
					$k=preg_replace('/[\/]{2,}/', '/', $k);									    //修正路径格式
					$curId = md5($k.$className);	
					//$fileExt = trim(substr($v,strpos($v,'.')+1,strlen($v)));
					$fileExt=extendName($v);
					if(in_array($fileExt,$keditFileTypes))
					{
						$treeHtml .= '<dt>-<a name="tree" type="file"  href="'.$k.'" title="编辑" id="'.$curId.'" class="'.$className.'" >'.$v.'</a></dt>'."\n";
					}
					elseif(in_array($fileExt,$uploadFileTypes))
					{
						$treeHtml .= '<dt>x<a name="tree" type="FILE" href="'.$k.'"  title="资源文件" id="'.$curId.'" class="'.$className.'" >'.$v.'</a></dt>'."\n";
					}
					elseif(in_array($fileExt,$fobbdnFileTypes))
					{//禁止操作
					}
				}
			}
		}
		$treeHtml .= "	</dl>\n";
	}	
	return $treeHtml;
}
/*
 * ajax 动态请求当前目录信息
 */
//获得当前目录下的 目录和文件集合
function getCurDirTree()
{
	global $request;
 	$curdir = filter_submitpath( $request['curdir'] );
	$curabsdir = get_abs_skin_root().$curdir;
	if(is_dir($curabsdir)){
		exit('1::'.treeArray( listDirTree($curabsdir), $curdir )); 
	}
	else exit('0::无此目录，禁止操作！');
}
//获得当前目录集合
function getCurDirs()
{
	global $request;
 	$curdir = filter_submitpath( $request['curdir'] );
	$curabsdir = get_abs_skin_root().$curdir;
	if(is_dir($curabsdir)){
		exit('1::'.treeArray( listDirTree($curabsdir), $curdir ,false )); 
	}
	else exit('0::无此目录，禁止操作！');
}
function getFileCode()
{
	global $request;
	$fileName = filter_submitpath( $request['fileName'] );
	$fname    = array_pop( explode('/',$fileName) );
	$keditFileTypes  = array('php','shtml','html','htm','xml','log','txt','js','css');
	$ext = extendName($fname);
	if(empty($ext) || !in_array($ext,$keditFileTypes))
	{
		exit('0::此类型文件不允许编辑');
	}
	$curFile  = get_abs_skin_root().$fileName;
	if(is_file($curFile)){
		$filesizelimit=array('php'=>200,'shtml'=>100,'html'=>100,'htm'=>100,'xml'=>50,'log'=>500,'txt'=>500,'js'=>300,'css'=>300);
		
		if( filesize($curFile)>1024*$filesizelimit[$ext])
		{
			exit('0::此文件超过'.$filesizelimit[$ext].'k！');
		}
		$str = file2String($curFile); 
		exit('1::'.$str);
	}elseif(is_dir($curFile)){
		exit('0::禁止操作！');
	}else
		die('0::此文件禁止操作！');
}

function saveFileCode()
{
	global $request,$fileCode;
	if(empty($request['fileCode']))die('数据为空！');
	$request['fileName'] = filter_submitpath( $request['fileName'] );	//过滤ok
	
	$fname    = array_pop( explode('/',$request['fileName']) );
	$keditFileTypes  = array('php','shtml','html','htm','xml','log','txt','js','css');
	$ext = extendName($fname);
	if(empty($ext) || !in_array($ext,$keditFileTypes))
	{
		exit('0::此类型文件不允许编辑');
	}
	$curFile = get_abs_skin_root().$request['fileName'];
	if(is_file($curFile))
	{
		$filesizelimit=array('php'=>100,'shtml'=>100,'html'=>100,'htm'=>100,'xml'=>50,'log'=>200,'txt'=>200,'js'=>300,'css'=>200);
		if(cnStrLen($request['fileCode'])>1024*$filesizelimit[$ext]) die('此文件超过'.$filesizelimit[$ext].'k，禁止操作！');
		$fileExt = trim(substr($request['fileName'],strpos($request['fileName'],'.')+1,strlen($request['fileName'])));
		
		if($fileExt=='php' || $fileExt=='html' || $fileExt=='htm' || $fileExt=='shtml' || $fileExt=='css' || $fileExt=='js' || $fileExt=='xml' || $fileExt=='log' || $fileExt=='txt')
		{
		/*还原 信息  开始*/
    		$str=str_replace('\n', '<--n-->', $request['fileCode']);   //换行符转义避免被下面的标签过滤掉反斜杠
			$str=stripslashes($str);					               //过滤文件敏感信息
			$str=str_replace('{##}','&',$str);						   //js转码替代方案
			$str=str_replace('{####}','+',$str);					   //连接符
			$str=str_replace('<--n-->', PHP_EOL, $str);                //转义后的换行符再转义回来
        /*还原 信息  结束*/
			if(mb_detect_encoding($str)!='UTF-8' && mb_detect_encoding($str)!='ASCII')
			{
				@unlink($curFile);
				string2file('尊敬的用户，该文件不是utf8编码 ，请将原文件代码手动粘贴到此,保存，并按提示修改',$curFile);
				exit('粘贴文本请先转码成utf8编码');
			}else{						
				string2file($str,$curFile);
				exit('编辑成功');
			}
		}
		else
		{
			exit('fobidden');
		}

	}
	elseif(is_dir($curFile))
	{
		exit('禁止操作！');
	}
	else
		die('此文件禁止操作！');
}

/*在用户目录下创建子目录*/
function createDir()
{
	global $request;
	$dirPath    = get_abs_skin_root().filter_submitpath( $request['dirPath'] );
	if(!is_dir($dirPath)) exit('0::上级目录不存在');
	$newName    = filter_submitname( $request['newFolder'] );
	$newDirPath = $dirPath.'/'.$newName;
	if(is_dir($newDirPath))
	{
		exit('0::目录已存在');
	}
	if(mkdir($newDirPath))
	{
		exit('1::目录创建成功');
	}else{
		exit('0::目录创建失败');
	}
}
/*创建文件 仅允许创建 php css js xml html shtml的默认文件*/
function createPHPFile()
{
	
	$extArr  = array('php');
	$content = "<?php\n";
	exit(createFile( $extArr, $content));
}
function createJSFile()
{
	
	$extArr  = array('js');
	$content = '/*js*/';
	exit(createFile( $extArr, $content));
}
function createCSSFile()
{
	
	$extArr  = array('css');
	$content = '@CHARSET "UTF-8";'."\n";
	exit(createFile( $extArr, $content));
}
function createXMLFile()
{
	
	$extArr  = array('xml');
	$content = '﻿<?xml version="1.0" encoding="utf-8"?>'."\n";
	exit(createFile( $extArr, $content));
}
function createHTMLFile()
{
	
	$extArr  = array('html', 'shtml');
	$content = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	$content.='<html>'."\n".'<head>';
	$content.='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
	$content.='<title>Insert title here</title>';
	$content.='</head>'."\n".'<body>';
	$content.='';
	$content.='</body>'."\n".'</html>';
	exit(createFile( $extArr, $content));
}
function createFile($extArr=array(),$content)
{
	global $request;
	$dirPath = filter_submitpath( $request['dirPath'] );
	$dirAbsPath = get_abs_skin_root().$dirPath;	//过滤ok
	if(!is_dir($dirAbsPath))
	{
		return '0::上级目录不存在';
	}
	$newName = filter_submitname( $request['newFile'] );
	$ext 	 = extendName($newName);
	if(empty($ext))
	{
		$newName.='.'.$extArr[0];
	}else{
		if(!in_array($ext,$extArr))
		{
			return '0::请确认创建的文件类型';
		}
	}
	$showNewFile = filter_submitpath( $dirPath.'/'.$newName );
	$newFile = $dirAbsPath.'/'.$newName;
	if(is_file($newFile))
	{
		return '0::'.$showNewFile.'文件已存在';
	}
	if(file_put_contents($newFile, $content))
	{
		chmod($newFile,0777);
		return '1::'.$showNewFile.'文件创建成功';
	}else{
		return '0::'.$showNewFile.'文件创建失败';
	}
}
/*上传资源  仅允许上传 图片 视频*/
function updateImageResource()
{
	
	$extArr  = array('jpg', 'gif', 'jpeg', 'png', 'ico', 'bmp');
	$opts    = array('filed'=>'newUpload','maxSize'=>150*1024);
	exit(uploadResource( $extArr, $opts));
}
function uploadVideoResource()
{
	
	$extArr  = array('flv','swf');
	$opts    = array('filed'=>'newUpload','maxSize'=>5*1024*1024);
	exit(uploadResource( $extArr, $opts));
}
function uploadResource($extArr=array(),$opts=array('filed'=>'newUpload','maxSize'=>0))
{
	$__files = $_FILES[$opts['filed']];
	if(empty($__files))
	{
		return '0::请上传文件';
	}
	if( $__files['size'] > 0 && $__files['size'] <= $opts['maxSize'])
	{
		global $request;
		$dirPath  = get_abs_skin_root().filter_submitpath( $request['dirPath'] );
		if(!is_dir($dirPath))
		{
			return '0::禁止操作！';
		}
		$newName = filter_submitname( $__files['name'] );
		$ext   = extendName($newName);
		$upExt = substr( $__files['type'], strpos($__files['type'],'/')+1, strlen($__files['type']) );
		if(!in_array($ext,$extArr))
		{
			return '0::文件类型不合法';
		}
		$newResource  = $dirPath.'/'.$newName;
		if(is_file($newResource))
		{
			return '0::存在同名文件。';
		}
		if(!move_uploaded_file($__files['tmp_name'],$newResource))
		{
			return '0::文件上传系统操作错误。';	
		}else{
			return '1::文件上传成功';
		}
	}
	elseif($__files['size']==0)
	{
		return '0::请上传的文件';
	}else{
		return '0::上传文件超过'.DisplayFileSize($__files['size']);
	}
}
/*重名名*/
function reNameFile()
{
	global $request;
	$dirPath   = filter_submitpath( $request['dirPath'] );
	$dirPathArr= explode('/',$dirPath);
	$oldName   = array_pop( $dirPathArr );
	$skinspath = get_abs_skin_root();
	$old   	   = $skinspath.$dirPath;
	$newName   = filter_submitname( $request['newName'] );
	if($oldName==$newName)
	{
		exit( '0::请换个名称');
	}
	if(!is_file($old))
	{
		exit( '0::原文件不存在');
	}
	$oldext   = extendName($oldName);
	$newext   = extendName($newName);
	if(empty($newext))
	{
		$newName.='.'.$oldext;
	}else{
		if($oldext!=$newext)
		{
			exit( '0::必须为同文件类型');
		}
	}
	$newName = filter_submitpath( @implode('/', $dirPathArr).'/'.$newName );
	$new     = $skinspath.$newName;
//排除移存在同路径文件
	if(is_file($new))
	{
		exit( '0::'.$newName.'文件已经存在');
	}
	if(rename($old, $new))
	{
		exit( '1::重名名成功');
	}else{
		exit( '0::重名名失败');
	}
}
function reNameDir()
{
	global $request;
	$dirPath   = filter_submitpath( $request['dirPath'] );
	$dirPathArr= explode('/',$dirPath);
	$oldName   = array_pop( $dirPathArr );
	$skinspath = get_abs_skin_root();
	$old   	   = $skinspath.$dirPath;
	$newFolder = filter_submitpath( $request['newFolder'] );
	$newFolderArr= explode('/',$newFolder);
	$newName   = array_pop( $newFolderArr );
	if($oldName==$newName)
	{
		exit( '0::请换个名称');
	}
	if(!is_dir($old))
	{
		exit( '0::原目录不存在');
	}
	$newName = str_replace('.','',$newName);
	$newName = filter_submitpath( @implode('/', $dirPathArr).'/'.$newName );
	$new     = $skinspath.$newName;
//排除移存在同路径目录
	if(is_dir($new))
	{
		exit( '0::'.$newName.'目录已经存在');
	}
	if(rename($old, $new))
	{
		exit( '1::重名名成功');
	}else{
		exit( '0::重名名失败');
	}
}
/*移动目标*/
function moveFile()
{
	global $request;
	$dirPath   = filter_submitpath( $request['dirPath'] );
	$newFolder = filter_submitpath( $request['newFolder'] );
	if(empty($newFolder))
	{
		exit( '0::请选择文件夹');
	}
	$skinspath = get_abs_skin_root();
	$old   	   = $skinspath.$dirPath;
	if(!is_file($old))
	{
		exit( '0::原文件不存在');
	}
	$dirPathArr= explode('/',$dirPath);
	$oldname   =array_pop( $dirPathArr );
	$oldFolder =@implode('/', $dirPathArr);
	$new = $skinspath.$newFolder;
	if(!is_dir($new))
	{
		exit( '0::'.$newFolder.'目录不存在');
	}
	$new .= '/'.$oldname;
//排除移动后不为同一文件
	if(is_file($new))
	{
		exit( '0::'.$dirPath.'文件已经存在');
	}
	if(copy($old, $new))
	{
		@unlink($old);
		exit( '1::移动成功');
	}else{
		exit( '0::移动失败');
	}
}
function moveDir()
{
	global $request;
	$dirPath   = filter_submitpath( $request['dirPath'] );
	$newFolder = filter_submitpath( $request['newFolder'] );
	if(empty($newFolder))
	{
		exit( '0::请选择文件夹');
	}
	if(strpos($newFolder,$dirPath)!==false)//目标为原子目录
	{
		exit( '0::不可以将原文件夹移到新文件夹');
	}
	$skinspath = get_abs_skin_root();
	$old   	   = $skinspath.$dirPath;
	if(!is_dir($old))
	{
		exit( '0::原目录不存在');
	}
	$dirPathArr= explode('/',$dirPath);
	$oldname   =array_pop( $dirPathArr );
	$new = $skinspath.$newFolder;
	//目标文件夹
	if(!is_dir($new))
	{
		exit( '0::'.$newFolder.'目录不存在');
	}
	$new.='/'.$oldname;
	//排除移动后不为同一目录
	if(is_dir($new))
	{
		exit( '0::'.$dirPath.'目录已经存在');
	}
	dir_copy($old,$new);
	del_dir($old );
	if(!is_dir($old)){
		exit( '1::移动成功');
	}else{
		exit( '0::移动失败');
	}
}
/*删除目标 */
function deleteFile(){
	global $request;
	$dirPath = get_abs_skin_root().filter_submitpath( $request['dirPath'] );
	if(is_file($dirPath)){
		@unlink($dirPath);
		exit('1::delete ok');
	}else{
		exit('0::Forbidden');
	}
}
function deleteDir(){
	global $request;
	$dirPath = get_abs_skin_root().filter_submitpath( $request['dirPath'] );
	if(is_dir($dirPath)){
		del_dir($dirPath );
		exit('1::delete ok');
	}else{
		exit('0::Forbidden');
	}
}
function delete() 
{
	global $request;
	del_dir(ABSPATH.'/'.SKINROOT.'/'.$request['skinname']);
	redirect('?m=system&s=changeskin');
}



//-----------------------------------------------------功能函数---------------------------------------//
/*返回当前目录的下的文件 与目录 */
function listDirTree($dirName=null)
{ 
	if(empty($dirName))
		exit("IBFileSystem: directory is empty.");
	if(!is_dir($dirName))
		exit("IBFileSystem: $dirName is not a directory.");
	if(!$dh=opendir($dirName))
		exit("IBFileSystem: can not open directory $dirName.");
	$tree=array();
	while(($file=readdir($dh))!==false)
	{
		if($file!="."&&$file!="..")
		{
			$filePath=$dirName."/".$file;
			$filename = trim(substr($filePath, strpos($filePath,SKINROOT.'/'.STYLENAME)+strlen(SKINROOT)+strlen(STYLENAME)+1, strlen($filePath)));
			if(is_dir($filePath)){
				$tree['dir'][$filename]=$file;	
			}else{
				$tree['file'][$filename]=$file;
			}
		}
	}
	closedir($dh); 
	return $tree;
}
function extendName($file_name)
{
	if( strpos( $file_name, '.' )===false )return '';
	$extend =explode(".", $file_name);
	$va=count($extend)-1;
	return strtolower($extend[$va]);
}
function del_dir($dir) {
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

	if(@rmdir($dir)) {
		return true;
	} else {
		return false;
	}
}
function change_skin_by_name($skinStr)
{
	//echo ABSPATH.'/skins/'.$skinStr.'/config.xml';
	//exit;
	if(is_file(ABSPATH.'/skins/'.$skinStr.'/config.xml'))
	{
		$tempStr = file2String(ABSPATH.'/config/doc-config-'.$_SESSION[TB_PREFIX.'doclang'].'.php');
		$newStr = preg_replace("/'STYLENAME','.*?'/i","'STYLENAME','$skinStr'",$tempStr);

		string2file($newStr,ABSPATH.'/config/doc-config-'.$_SESSION[TB_PREFIX.'doclang'].'.php');
		@chmod(ABSPATH.'/config/doc-config-'.$_SESSION[TB_PREFIX.'doclang'].'.php', 0666);
		//del_file(ABSPATH.'/skins_c');
		return true;
	}
	else 
	{
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
/**
 * 获取模板文件中的网站样式名称
 *
 * @return string
 */
function get_skin_config()
{
	global $request;
	$tempStr = file2String(ABSPATH.'/config/doc-config-'.$_SESSION[TB_PREFIX.'doclang'].'.php');
	preg_match("/'STYLENAME','(.*?)'/i",$tempStr, $matches);
	return $matches[1];
}
/**
 * 获取模板文件的信息
 * 返回数组
 *
 * @param array $tArr
 */
function get_skins_info($tArr)
{
	$configs=array();
	$arraynum=0;
	foreach ($tArr as $o)
	{
		$xmlFilePath= ABSPATH."/skins/".$o.'/config.xml';
		if(is_file($xmlFilePath))
		{
			$doc = new DOMDocument(null,'utf-8');

			$doc->load($xmlFilePath);
			$configs[$arraynum]['path']=$o;
			$configs[$arraynum]['pic']=ROOTPATH.'/skins/'.$o.'/'.trim($doc->getElementsByTagName('smallPic')->item(0)->nodeValue);
			$configs[$arraynum]['siteName']=$doc->getElementsByTagName('title')->item(0)->nodeValue;
			$configs[$arraynum]['FileName']=$doc->getElementsByTagName('skins')->item(0)->nodeValue;
			$configs[$arraynum]['SiteMaster']=$doc->getElementsByTagName('author')->item(0)->nodeValue;
			$configs[$arraynum]['PubDate']=$doc->getElementsByTagName('pubDate')->item(0)->nodeValue;
			$configs[$arraynum]['summary']=$doc->getElementsByTagName('description')->item(0)->nodeValue;
		}
		$arraynum=$arraynum+1;
	}
	$useSkin=get_skin_config();
	foreach ($configs as $entry)
	{
		if($useSkin==$entry['path'])
		{
			echo '<dl class="u_box">';
		}
		else
		{
			echo '<dl class="skinbox">';
		}
		echo '<dd><a href="./index.php?m=system&s=changeskin&a=changeskin&skin='.$entry['path'].'" onmouseover="ddrivetip(\''.trim($entry['summary']).'\', \'\', 220)" onmouseout="hideddrivetip()"><img width="220" height="165" src="';
		//echo is_file(ABSPATH.$entry['pic'])?$entry['pic']:"images/none.jpg";
		echo $entry['pic'];
		echo '" /></a></dd>';
		echo '<dd>名称：'.$entry['siteName'].'</dd>';
		echo '<dd>标识：'.$entry['FileName'].'</dd>';
		echo '<dd>作者：'.$entry['SiteMaster'].'</dd>';
		echo '<dd>日期：'.$entry['PubDate'].'</dd>';
		echo '<dd><a href="./index.php?m=system&s=changeskin&a=editSkin&skin='.$entry['path'].'" class="creatbt" onmouseover="ddrivetip(\'点击编辑此模版\', \'\', 90)" onmouseout="hideddrivetip()">模板修改</a><a href="./index.php?m=system&s=changeskin&a=changeskin&skin='.$entry['path'].'" class="creatbt" onmouseover="ddrivetip(\'点击使用此模版\', \'\', 90)" onmouseout="hideddrivetip()">使用</a>&nbsp;&nbsp;<a href="?m=system&s=changeskin&a=delete&skinname='.$entry['path'].'" class="creatbt" onmouseover="ddrivetip(\'点击删除此模板\', \'\', 90)" onclick="return confirm(\'您确认要删除本模板?\n一旦删除，此模板将不可恢复。\');">删除</a></dd>';
		echo '</dl>';
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
?>