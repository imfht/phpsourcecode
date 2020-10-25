<?php
/**
 * TXTCMS 首页模块
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class IndexAction extends AdminAction {
	public function _init(){
		parent::_init();
	}
	public function index(){
		$this->display();
	}
	public function main(){
		$this->assign($_SERVER);
		$this->assign('php_libz',extension_loaded("zlib")?'On':'<font color=red>Off</font>');
		$this->assign('display_errors',ini_get('display_errors')?'On':'Off');
		$this->assign('upload_max_filesize',ini_get('upload_max_filesize'));
		$this->assign('magic_quotes_gpc',(get_magic_quotes_gpc()==1)?'On':'Off');
		$curl_init=function_exists('curl_init') && function_exists('curl_exec') ? '<span style="color:green">curl_init</span>' : '<span style="color:red">curl_init</span> ';
		$fsock=function_exists('fsockopen') ? '<span style="color:green"> fsock</span>，' : '<span style="color:red"> fsockopen</span> ';
		$pfsock=function_exists('pfsockopen')? '<span style="color:green"> pfsock</span>，' : '<span style="color:red"> pfsockopen</span> ';
		$file_get_contents=function_exists('file_get_contents') ? '<span style="color:green"> file_get_contents</span>' : '<span style="color:red"> file_get_contents</span>';
		$mbstring= (function_exists('mb_strlen') ? '<font color=green>On</font>' : '<font color=red>Off</font>');
		$this->assign('mbstring',$mbstring);
		$this->assign('iconv',function_exists('iconv') ? '<span style="color:green">On</span>' : '<span style="color:red">Off</span>');
		$this->assign('zendoptimizer',defined("OPTIMIZER_VERSION")?OPTIMIZER_VERSION: '<font color=red>未安装</font>');
		$fetch_mode=$curl_init.'(推荐)'.$fsock.$pfsock.$file_get_contents;
		$this->assign('fetch_mode',$fetch_mode);
		$php_os = explode(" ", php_uname());
		$this->assign('php_os',$php_os[0].'&nbsp;内核版本：'.(('/'==DIRECTORY_SEPARATOR)?$php_os[2]:$php_os[1]));
		if(test_write(TEMP_PATH)){
			$data['tips']='<b>temp</b> <img src="static/images/success.png">';
		}else{
			$data['tips']='<b>temp</b> <img src="static/images/error.png">';
		}
//		if(test_write(TMPL_PATH)){
//			$data['tips'].=' <b>template</b> <img src="static/images/success.png">';
//		}else{
//			$data['tips'].=' <b>template</b> <img src="static/images/error.png">';
//		}
		if(test_write(APP_ROOT.'uploads')){
			$data['tips'].=' <b>uploads</b> <img src="static/images/success.png">';
		}else{
			$data['tips'].=' <b>uploads</b> <img src="static/images/error.png">';
		}
		if(is_file(config('ROBOT_FILE'))){
			$arr=file(config('ROBOT_FILE'));
			$arr=str_replace(array("\r\n","\r","\n"),'',$arr);
			$data['robot_count']=count($arr);
		}else{
			$data['robot_count']=0;
		}
		$data['article_count']=DB('article')->count();
		$data['class_count']=DB('arctype')->count();
		$data['flink_count']=DB('flink')->count();
		$data['logtime']=date('Y-m-d H:i',$_SESSION['admin']['logtime']);
		$data['logip']=$_SESSION['admin']['logip'];
		$this->assign($data);
		$this->display();
	}
}