<?php
/**
 * 静态生成
 */
checkme(9);
function index(){
	global $data;
	global $request;
	$path=filter_submitpath($request['path']);

	$root=ABSPATH.HTMLPATH;
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
	
	$root=ABSPATH.HTMLPATH;
	
	$isDir=false;
	if($request['path'] == '/' || $request['path'] == '')
	{
		$deep = 0;
	}
	else
	{
		$deep = 1;
	}
	if(is_dir($delpath=$root.$path)){
		_delDirs($delpath,$deep);
		$isDir=false;
	}
	if(!$isDir && is_file($delpath)){
		@unlink($delpath);
	}
	redirect('?m=system&s=managehtml&path='._getParentNode(filter_submitpath($request['path'])));	
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
function _delDirs($dir,$deep=0) 
{
	global $menus,$request;
	foreach($menus as $v)
	{
		$dirArray[]=$v['menuName'];
	}
	$dh = opendir($dir);
	while ($file=readdir($dh)) {
		if($file!="." && $file!=".." && ( ($deep == 0  && (in_array($file,$dirArray) || $file=='index.html')) || $deep>0 )){
			$fullpath = $dir."/".$file;
			if(!is_dir($fullpath)) {
				@unlink($fullpath);
			} else {
				_delDirs($fullpath,1);
			}
		}
	}
	closedir($dh);
	return rmdir($dir);
}
//列出某文件夹内所有目录
function _getDirsOrFiles($from = '.')
{
	global $menus,$request;
	foreach($menus as $v)
	{
		$dirArray[]=$v['menuName'];
	}
	$data = array('d'=>array(),'f'=>array());
    if(!is_dir($from))return $data;
    if( $dh = @opendir($from))
    {
		if($request['path']=='')$request['path']='/';
        while( false !== ($file = readdir($dh)))
        {
            if( $file != '.' && $file != '..' && ( ( $request['path'] == '/'  && (in_array($file,$dirArray) || $file=='index.html')) || $request['path'] != '/' )){
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
/**
 * 静态一键生成
 * @auther  grysoft(狗头巫师)
 * @time 2012.12.12
 * 技术支持QQ:767912290
 */
function gohtml()
{
}
function htmlCount()
{
	global $db,$menus,$substr,$request;
	
	$html = HTMLPATH;
	// 获取所有频道栏目数据
	$sql = 'SELECT * FROM '.TB_PREFIX.'menu';
	$rs = $db->get_results($sql);
	$request['cs'] = intval($request['cs']);
	$cs=0;
	
	foreach($rs as $o)
	{
		//生成所有频道栏目页的静态文件
		if($cs == $request['cs']) 
		{
			$result = createHTML($html,0,$o->id);
			if(!empty($result))
				echo '<p>'.$result.'已生成</p>';
			else
				echo '<p>'.$result.'</p>';
			exit;
		}
		//频道栏目下的其他页面生成
		$temptype = array('list','jobs','guestbook','picture','poll','product','video');
		if(in_array($o->type,$temptype))
		{
			$sql2 = 'SELECT * FROM '.TB_PREFIX.$o->type.' WHERE channelId ='.$o->id;
			$rsone = $db->get_results($sql2);
			if($rsone)
			{
				if($o->type =='poll')
				$o->type = 'product';
					
				eval('$syscount = '.$o->type.'Count;');
				
				if($o->type == 'linkers')
				$syscount = 100;
				
				//生成所有列表页的分页静态页
				if(count($rsone)>$syscount)
				{
						$counts = ceil(count($rsone)/$syscount); //取整
						for($i=1;$i<=$counts;$i++)
						{
							$cs++;
							if($cs == $request['cs'])
							{
								$result = createHTML($html,$i,$o->id);
								if(!empty($result))
									echo '<p>'.$result.'已生成</p>';
								else
									echo '<p>'.$result.'</p>';
								exit;
							}
						}
				}
				//生成所有数据 详情页的静态页
				foreach($rsone as $v)
				{
					$cs++;
					if($cs == $request['cs'])
					{
						$result = createHTML($html,0,$o->id,$o->type,'view',$v->id);
						if(!empty($result))
							echo '<p>'.$result.'已生成</p>';
						else
							echo '<p>'.$result.'</p>';
						exit;	
					}
				}
			}
		}
		$cs++;
	}
	$result = createHTML($html,0,0);
	if(!empty($result))
		echo '<p>'.$result.'已生成</p>';
	else
		echo '<p>'.$result.'</p>';
	exit;
}
function createHTML($html='html',$count=0,$id=0,$model=0,$action=0,$args=0)
{
	$www = $_SERVER['SERVER_NAME']=='localhost'?'127.0.0.1:'.$_SERVER["SERVER_PORT"]:$_SERVER['HTTP_HOST'];
	$url =  'http://'.$www.ROOTPATH.'/admini/html.php?p='.$id.'&a='.$action.'&r='.$args;
	if(!availableUrl($url)){
		return '404 页面不存在！';
	}
	$contents = file_get_contents($url);
	
	$powerby = '<!--本网站由稻壳企业建站系统生成 Power by DocCms x1.0&DoooC.com -->';
	//页面缓存
	$_html_root=ABSPATH.$html;

	if($id == '0')
	{
		$htmlfile = $_html_root.'/index.html';
		$url= $html.'/index.html';
	}
	elseif($args)
	{
		$url = sys_href($id,$model,$args);
		$htmlfile = $_html_root.$url;
		$url = $html.$url;
	}
	else
	{
		if($count)
		{
			$page = $count.'/';
		}
		$url = sys_href($id);
		$htmlfile = $_html_root.$url.$page.'index.html';
		$url = $html.$url.$page.'index.html';
	}
	$contents .=$powerby;
	$path=dirname ( $htmlfile );
	if(!isset($hasError)){
		createFile($path,$htmlfile,$contents);
	}
	return $url;
}
/*递归创建目录*/
function createFolders($path)
{
	if (!is_dir($path)){
		createFolders(dirname($path));
		mkdir($path, 0777);
	}
}
/*创建并写文件*/
function createFile($path,$filename,$content)
{
	createFolders($path);
	file_put_contents($filename,$content);
	chmod($filename,0777);
}
/*判断URL 是否存在*/
function availableUrl($url) {
	// 避免请求超时超过了PHP的执行时间
	$executeTime = ini_get('max_execution_time');
	ini_set('max_execution_time', 0);
	$headers = @get_headers($url);
	ini_set('max_execution_time', $executeTime);
	if ($headers) {
		$head = explode(' ', $headers[0]);
		if (!empty($head[1]) && intval($head[1]) < 400)
			return true;
	}
}