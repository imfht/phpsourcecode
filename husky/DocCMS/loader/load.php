<?php
//网站关闭时输出的语句
WEBOPEN?'':exit('网站维护中。。。');

$url_this=isset($_SERVER['HTTP_X_REWRITE_URL']) ? $_SERVER['HTTP_X_REWRITE_URL'] : $_SERVER['REQUEST_URI'];
$url_this=substr($url_this,1,strlen($url_this)-1);

//default  加载模板下的引导页
if(!$url_this){	
	$skinRoot=get_abs_skin_root();
	$default=array('default.html','default.htm','default.php');
	foreach($default as $v){
		if(is_file($skinRoot.$v)){
			$htmlfile=$skinRoot.$v;
			include($htmlfile);
			exit();
		}
	}	
}
require_once(ABSPATH.'/loader/doc.php');
/*递归创建目录*/
function createFolders($path){
	if (!is_dir($path)){
		createFolders(dirname($path));
		mkdir($path, 0777);
	}
}
/*创建并写文件*/
function createFile($path,$filename,$content){
	createFolders($path);
	file_put_contents($filename,$content);
	chmod($filename,0777);
}
//静态化 URL强制跳转
function HTML_load(){
	global 	$params,$url_this,$htmlfile;
	if(URLREWRITE){	
		if(stristr($url_this,'?p') || stristr($url_this,'?m')){
			if($params['args'])
				$url = sys_href($params['id'],$params['model'],$params['args']);
			elseif($request['m'])
				$url = sys_href($params['id'],$params['model']);
			else
				$url = sys_href($params['id']);
			redirect($url);
		}
		//静态加载
		$_html_root=ABSPATH.HTMLPATH.'/';
		if(isset($_SERVER['HTTP_X_REWRITE_URL'])){ //IIS 服务器下加载
			if($params['args']){
				$url = sys_href($params['id'],$params['model'],$params['args']);
				$htmlfile = $_html_root.$url;
			}
			else{
				$url = sys_href($params['id']);
				$htmlfile = $_html_root.$url.'index.html';
			}
			if(is_file($htmlfile) && time()<filemtime($htmlfile)+CACHETIME){
				include($htmlfile);
				exit();
			}
		}
		else{                              //其他服务器下加载
			if( strpos(substr($url_this,-5,5), '.')!==false ){
				$htmlfile=$_html_root.$url_this;
			}else{
				$htmlfile=$_html_root.$url_this.'/index.html';
			}
			if(is_file($htmlfile) && time()<filemtime($htmlfile)+CACHETIME){
				
				include($htmlfile);
				exit();
			}
		}
	}
}
//页面加载
function PAGE_load($include){
	global $tag,$params,$htmlfile;
	$powerby = urldecode('%3C%21--%E6%9C%AC%E7%BD%91%E7%AB%99%E7%94%B1%E7%A8%BB%E5%A3%B3%E4%BC%81%E4%B8%9A%E5%BB%BA%E7%AB%99%E7%B3%BB%E7%BB%9F%E7%94%9F%E6%88%90+Power+by+DocCms+x1.0%26DoooC.com+--%3E');
	$noHtmlModule = array( 'user','search','pay' );
	if( defined('CACHETIME') && CACHETIME && !in_array($params['model'],$noHtmlModule) && function_exists(ob_start)){
		
		ob_start(); 
		require_once($include);
		$contents=ob_get_contents();
		ob_end_clean();
		$contents .=$powerby;
		$path=dirname ( $htmlfile );
		if(!isset($hasError)){
			createFile($path,$htmlfile,$contents);
		}
		echo $contents;
	}
	else{
		if(function_exists(ob_start)){
			ob_start(); 
			require_once($include);
			$contents=ob_get_contents();
			ob_end_clean();
			$contents .=$powerby;
			echo $contents;
		}
		else{
			require_once($include);
			echo $powerby;
		}
	}
}
//内页parts 主体函数
function sys_parts($style=0){
	global $request,$params,$tag,$path,$data,$stylename;
    // 404 转向
	if(empty($params['model']))
	redirect($tag['path.root'] .'/404.html');
	
	$part_path=ABSPATH.'/skins/'.$stylename.'/parts/'.$params['model'].'/'.$params['model'].'_'.$params['action'].'_'.$style.'.php';
	if(is_file($part_path))
		require_once($part_path);
	else 
		echo '<span style="color:RED"><strong>加载 /skins/'.$stylename.'/parts/'.$params['model'].'/'.$params['model'].'_'.$params['action'].'_'.$style.'.php 样式资源文件失败，程序意外终止。</strong></span>';
    //评论模块开启	
	isComments();
}
function get_model_type($id){
	global $db;
	if($id==0)
	return array('type'=>'index','level'=>0);
	else
	return $db->get_row("SELECT * FROM ".TB_PREFIX."menu WHERE id=$id",ARRAY_A);
}
function getIdByMenuName($file){
	global $db;
	$sql="SELECT id FROM ".TB_PREFIX."menu WHERE menuName='$file'";
	$result = $db->get_var($sql);
	if($result)
	return $db->get_var($sql);
	else
	redirect($tag['path.root'] .'/404.html');
}