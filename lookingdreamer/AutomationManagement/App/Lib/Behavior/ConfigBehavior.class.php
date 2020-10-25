<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class ConfigBehavior extends Behavior {

    protected $options = array(
    
    );
    
    public function run(&$params){
    	$this->LoadConfig();
    }
    
    private function LoadConfig(){
    	include './App/Conf/seophp_version.php';
    	$autokey = "";
			// 读取系统配置参数
      if(!file_exists(DATA_PATH.'~config.php')) {
            $config		=	M("Config");
            $list			=	$config->getField('name,value');
            $savefile		=	DATA_PATH.'~config.php';
            // 所有配置参数统一为大写
            $content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_UPPER),true).";\n?>";
            if(!file_put_contents($savefile,$content)){
                $this->error('配置缓存失败！');
            }
      }
      C('NOW_TIME', time());
			C(include_once DATA_PATH.'~config.php');
			C(include_once DATA_PATH.'~_datacall_config.php');
			
			if(empty($_SERVER['PATH_INFO'])) {
			     $types   =  explode(',','REQUEST_URI,ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL');
			     foreach ($types as $type){
			         if(!empty($_SERVER[$type])) {
			              $_SERVER['PATH_INFO'] = (0 === strpos($_SERVER[$type],$_SERVER['SCRIPT_NAME']))?
			                  substr($_SERVER[$type], strlen($_SERVER['SCRIPT_NAME']))   :  $_SERVER[$type];
			              break;
			         }
			     }
			}
			$path_info = explode('/', $_SERVER['PATH_INFO']);
			foreach($path_info as $key => $val){
				if($val){
					if(is_dir('./'.$val)){
						$WEMOD = $val;
						unset($path_info[$key]);
					}
					break;
				}else{
					unset($path_info[$key]);
				}
			}
			$urls = implode('/', $path_info);
			$_SERVER['PATH_INFO'] = '/'.$urls;
			$depr = C('URL_PATHINFO_DEPR');
			if(!$depr)$depr = '/';
			C('URL_PATHINFO_DEPR_READ', $depr);
			if(strstr($_SERVER['PATH_INFO'], '/Admin')){
				C('DEFAULT_THEME', 'Default');
				C('URL_PATHINFO_DEPR', '/');
				C('URL_MODEL', 1);
			}elseif(!strstr($urls, $depr)){
				$UrlModel = M("urls");
				$urlmod = $UrlModel -> where("url='".$urls."'") -> find();
				if($urlmod)$_SERVER['PATH_INFO'] = "/".$urlmod['module'].$depr.$urlmod['modid'];
			}
    }
}
?>