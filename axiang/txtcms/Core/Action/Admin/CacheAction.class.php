<?php
/**
 * TXTCMS 缓存设置模块
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class CacheAction extends AdminAction {
	public $confile;
	public function _init(){
		parent::_init();
		$this->confile=TEMP_PATH.'config.php';
	}
	public function index(){
		$this->display();
	}
	public function update(){
		$config=$_POST['con'];
		foreach( $config as $k=> $v ){
			$config[$k]=trim($config[$k]);
		}
		$ajax=array();
		$ajax['status']=1;
		$config_old = require $this->confile;
		$config_new =array_merge($config_old,$config);
		ksort($config_new);
		arr2file($this->confile,$config_new);
		$this->ajaxReturn($ajax);
	}
	//清除缓存
	public function clear(){
		$robotFile=config('ROBOT_FILE');
		if(is_file($robotFile)){
			$data['robot_cache']=convSize(filesize($robotFile));
		}else{
			$data['robot_cache']=0;
		}
		$data['compile_cache']=convSize(getDirSize(TPLCACHE_PATH),'MB');
		$data['session_cache']=convSize(getDirSize(SESSION_PATH),'MB');
		$this->assign($data);
		$this->display();
	}
	//获取缓存大小
	public function del(){
		$file=isset($_POST['file'])?$_POST['file']:$this->ajaxReturn(array('status'=>0,"info"=>"参数不完整！"));
		$action=isset($_POST['action'])?$_POST['action']:$this->ajaxReturn(array('status'=>0,"info"=>"参数不完整"));
		$arr=array('compile','html','robotlog','session');
		if(in_array($file,$arr)){
			if($file=='robotlog'){
				$file=config('ROBOT_FILE');
			}elseif($file=='compile'){
				$file=TPLCACHE_PATH;
			}elseif($file=='html'){
				$file=CACHE_PATH.'Html';
			}elseif($file=='session'){
				$file=SESSION_PATH;
			}
		}else{
			$this->ajaxReturn(array('status'=>0,"info"=>"未在指定参数内"));
		}
		if($action=='del'){
			if(is_dir($file)){
				if(deldir($file)){
					$this->ajaxReturn(array('status'=>1));
				}else{
					$this->ajaxReturn(array('status'=>0,"info"=>"清除失败！"));
				}
			}else if(is_file($file)){
				if(unlink($file)){
					$this->ajaxReturn(array('status'=>1));
				}else{
					$this->ajaxReturn(array('status'=>0,"info"=>"清除失败！"));
				}
			}else{
				$this->ajaxReturn(array('status'=>0,"info"=>"文件不存在！"));
			}
		}
		if($action=='checksize'){
			if(is_dir($file)){
				$result=convSize(getDirSize($file));
			}else{
				$result=0;
			}
			$this->ajaxReturn(array('status'=>1,"size"=>$result.' MB'));
		}
	}
}