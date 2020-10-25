<?php
class TxtcmsAction extends Action{
	public function _init(){
		config('cms_name','TXTCMS');
		config('web_path',__ROOT__);
		config('tpl_path',__ROOT__.'Template');
		if(!config('web_debug')){
			error_reporting(0);
		}else{
			@ini_set('display_errors', 'On');
		}
		$lock = TEMP_PATH.'install.lock';
		if (!is_file($lock)) {
			//$this->assign("jumpUrl",U('Admin/Install/index'));
			//$this->error('您还没安装本程序，请进入安装!');
		}
		$this->assign(config());
	}
	//屏蔽IP访问，支持通配符*、C段
	public function banip(){
		$iplist=explode("|",config('web_banip_list'));
		$ip=get_client_ip();
		$banmsg='Error: Your IP has been temporarily banned';
		if(config('web_banip') && !empty($iplist)){
			foreach( $iplist as $v ){
				if(strpos($v,'~')){
					$ips=explode('~',$v);
					$s1=substr(strrchr($ips[0],'.'),1);
					$s2=substr(strrchr($ips[1],'.'),1);
					$s3=substr(strrchr($ip,'.'),1);
					$ss1=substr($ips[0],0,strrpos($ips[0],'.'));
					$ss2=substr($ips[1],0,strrpos($ips[0],'.'));
					$ss3=substr($ip,0,strrpos($ips[0],'.'));
					if(strcmp($ss1,$ss2)==0 && strcmp($ss2,$ss3)==0 && $s1<=$s3 && $s3<=$s2 ){
						exit($banmsg);
					}
				}else{
					$v=str_replace(array('*','.'),array('\\d+','\.'),$v);
					if(preg_match('/^'.$v.'$/',$ip)){
						exit($banmsg);
					}
				}
			}
		}
	}
}