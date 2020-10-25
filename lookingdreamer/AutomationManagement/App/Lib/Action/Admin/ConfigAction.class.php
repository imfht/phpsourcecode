<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

// 配置模块
class ConfigAction extends CommonAction {

		public function _before_insert(){
				$this->checkAdmin();
		}
		
		public function _before_update(){
				$this->checkAdmin();
		}
		
		public function _before_delete(){
				$this->checkAdmin();
		}		
		
		public function _before_edit(){
				$this->checkAdmin();
		}		

		// 批量修改配置参数
    public function saveConfig()
    {
    		$cg = $_POST['cg'];
    		$this->upload();
        $Config = M("Config");
				$cg_method = '_tigger_'.$cg;
	      if(method_exists($this,$cg_method)) {
	          $this->$cg_method();
	      }
	    	foreach($_POST as $key=>$val) {
	    			$val = stripslashes($val);
	    			if($key=='COMPANY_INTRO')$val = htmlspecialchars_decode($val);
	          $config    = Array();
	          $config['value']  =  $val;
	          $where =  "name='".$key."'";
	    			$Config->where($where)->save($config);
	    	}
				$this->updatecache();
        $this->success('配置修改成功！');
    }    

    public function saveConfig_database()
    {
    		$confile = CONF_PATH.'config.php';
    		$config = include($confile);
    		foreach($_POST as $key => $val) {
    				if($config[$key])$config[$key] = $val;
    		}
    		$content = "<?php\nreturn ".var_export(array_change_key_case($config,CASE_UPPER),true).";\n?>";
				file_put_contents($confile,$content);
				$this->success('数据库配置修改成功！');
    }
    
		public function setting() {
				$cg = $_GET['group'];
				configGroup($groups);
				if(!$groups[$cg])$cg = 'site';
				$group = $groups[$cg];
				if($group['admin'])$this->checkAdmin();
				$Config = M('Config');
				$list = $Config->where("cg='".$cg."'")->order('sort ASC, id ASC')->select();
				$this->assign("this_cg",$group);
				$this->assign("list",$list);
				$cg_method = 'setting_'.$cg;
/* 				dump($list);
				die() */;
	      if(method_exists($this,$cg_method)) {
	          $this->$cg_method();
	      }
				if(file_exists(THEME_PATH.'Config/config_'.$cg.'.html')){
						$this->display('config_'.$cg);
				}else{
						$this->display();
				}
		}
		
		public function setting_database(){
				$list = include(CONF_PATH.'config.php');
				$this->assign("vo",$list);
		}
		
		public function index()
	  {
	      //列表过滤器，生成查询Map对象
	      if(!isset($_REQUEST['_order']))$_REQUEST['_order'] = "sort";
	      if(!isset($_REQUEST['_sort']))$_REQUEST['_sort'] = 1;
	      $map = $this->_search();
	      if(method_exists($this,'_filter')) {
	          $this->_filter($map);
	      }
				$model = M($this->getActionName());
	      if(!empty($model)) {
	        	$this->_list($model,$map);
	      }
				$this->display();
	      return;
	  }		

		// 缓存配置文件
		public function cache($name='',$field='') {
				if($this->updatecache()){
					$this->success('配置缓存生成成功！');
				}else{
					$this->error('配置缓存失败！');
				}
		}
		
		protected function updatecache(){
				$config		=	M("Config");
				$list			=	$config->getField('name,value');
				foreach($list as $key => $val){
						preg_match_all('/\|\|\|/i', $val, $match);
						if($match[0]){
							$list[$key] = explode('|||', $val);
							foreach($list[$key] as $vo){
									$list[$key.'_'.$vo] = true;
							}
						}
				}
				$savefile		=	DATA_PATH.'~config.php';
				// 所有配置参数统一为大写
				$content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_UPPER),true).";\n?>";
				return file_put_contents($savefile,$content);
		}
}
?>