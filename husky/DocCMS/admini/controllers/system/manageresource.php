<?php
/**
 * 资源管理
 *  
 * 目录 --新建   删除    
 * 文件 --上传   删除  
 * 
 *  高级功能----删除
 *  目录-------打包   下载  
 *  选中文件---打包   下载   解压缩
 */
checkme(10);
function index(){
	global $data;
	global $request;
	$path=filter_submitpath($request['path']);
	$root=ABSPATH.UPLOADPATH;
	if(!is_dir($root))mkdir($root,0777);
	$from=$root.$path;
	$data=_getDirsOrFiles($from);
	$data['path']=str_replace($root,'',$from);
	$data['path']=empty($data['path'])?'/':$data['path'];
	$data['parent']=_getParentNode($path);
	$data['static']='';
	if($dNum=count($data['d']))$data['static'].=$dNum.'目录 '; 
	if($fNum=count($data['f']))$data['static'].=$fNum.'文件'; 
}

function cleanCache()
{
	global $request;
	$path=filter_submitpath($request['path']);
	$root = ABSPATH.UPLOADPATH;
	$isDir=false;
	if(is_dir($delpath=$root.$path)){
		_delDirs($delpath);
		$isDir=true;
	}
	if(!$isDir && is_file($delpath)){
		@unlink($delpath);
	}
	redirect('?m=system&s=manageresource&path='._getParentNode(filter_submitpath($request['path'])));	
}
function buildSystemDir(){
	$root = ABSPATH.UPLOADPATH;
	$sysdir=array('/File','/Flash','/Image','/Media','/Temp');
	foreach($sysdir as $v){
		if(!is_dir($path=$root.$v)){
			mkdir($path,0777);
		}
	}
	redirect('?m=system&s=manageresource&path=/');
}
function help(){
	
}
function _getParentNode($path)
{
	$tmp=explode('/',$path);
	$len=count($tmp);
	if(empty($tmp[$len-1]))unset($tmp[$len-2]);
	unset($tmp[$len-1]);
	$path=implode('/',$tmp);
	return $path.'/';
}
//清理目录
function _delDirs($dir) 
{
	$dh = opendir($dir);
	while ($file=readdir($dh)) {
		if($file!="." && $file!="..") {
			$fullpath = $dir."/".$file;
			if(!is_dir($fullpath)) {
				@unlink($fullpath);
			}else{
				_delDirs($fullpath);
				//rmdir($fullpath);
			}
		}
	}
	closedir($dh);
	return rmdir($dir);
}
//列出某文件夹内所有目录
function _getDirsOrFiles($from = '.')
{
	$data = array('d'=>array(),'f'=>array());
    if(!is_dir($from))return $data;
    if( $dh = opendir($from))
    {
        while( false !== ($file = readdir($dh)))
        {
            if( $file != '.' && $file != '..'){
	            $path = $from.'/'.$file;
	            if( is_dir($path) )
	              $data['d'][$path] = $file;
	            else
	               $data['f'][$path] = $file;
            }
        }
        closedir($dh);
    }
    return $data;
}

/*在用户目录下创建子目录*/
function createDir()
{
	global $request;
	$dirPath    = ABSPATH.UPLOADPATH.filter_submitpath( $request['dirPath'] );
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

function updateResource()
{
	$extArr  = array('jpg', 'gif', 'jpeg', 'png','bmp','flv','swf','txt','mp3','mid','xml','rar','zip','gz','pdf','doc','docx','xls','xlsx','ppt','pptx');
	$opts    = array('filed'=>'newUpload','maxSize'=>5*1024*1024);
	exit(_uploadResource($extArr, $opts));
}
function _uploadResource($extArr=array(),$opts=array('filed'=>'newUpload','maxSize'=>0))
{
	$__files = $_FILES[$opts['filed']];
	if(empty($__files))
	{
		return '0::请上传文件';
	}
	if( $__files['size'] > 0 && $__files['size'] <= $opts['maxSize'])
	{
		global $request;
		$dirPath  = ABSPATH.UPLOADPATH.filter_submitpath( $request['dirPath'] );
		if(!is_dir($dirPath))
		{
			return '0::禁止操作！';
		}
		$newName = filter_submitname( $__files['name'] );
		$ext   = extendName($newName);
		if(preg_match('/[^a-zA-Z0-9_-]+/i', str_replace('.'.$ext,'',$newName)))
		{
			return '0::请使用字母数字组合文件名！';	
		}
		$upExt = substr( $__files['type'], strpos($__files['type'],'/')+1, strlen($__files['type']) );
		if(!in_array($ext,$extArr))
		{
			return '0::文件类型不合法';
		}
		$newResource  = $dirPath.'/'.$newName;
		//$newResource  = $dirPath.'/'.date('YmdHis').'.'.$ext;
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
/*提供下载*/
function download()	
{
	global $request;
	$filename=filter_submitname($request['filename']);
	$ext   = extendName($filename);
	$backupfile=ABSPATH.UPLOADPATH.filter_submitpath( $request['path']).$filename;
	if(is_file($backupfile)){
		header('Content-type: application/'.$ext);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		readfile($backupfile);
		exit;
	}else{
		exit($filename.'文件 不存在！');
	}
}
function extendName($file_name)
{
	if( strpos( $file_name, '.' )===false )return '';
	$extend =explode(".", $file_name);
	$va=count($extend)-1;
	return strtolower($extend[$va]);
}
function viewfile(){
	
}
?>