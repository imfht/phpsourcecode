<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model;

class ModuleModel extends \Common\Model\BaseModel{

	protected $tableName = 'module';

	public function getAll(){
		$module = S('module_all');
		if (empty($module)) {
			$dir = $this->getFile(APP_PATH);
			foreach ($dir as $subdir) {
				if (file_exists(APP_PATH . '/' . $subdir . '/Info/info.php')) {
					$info = $this->getInfo($subdir);
					$info = $this->getModule($subdir);
					//卸载文件不存在
					if(file_exists(APP_PATH . '/' . $info['name'] . '/Info/uninstall.sql')){
						//卸载 = 1
						$info['can_uninstall'] = 1;
					}else{
						//其他的  如果 安装文件不存在 那么则是系统默认的模块 不能卸载的
						//如果安装文件存在
						if(file_exists(APP_PATH . '/' . $info['name'] . '/Info/install.sql')){
							$info['can_uninstall'] = 1;
						}else{
							$info['can_uninstall'] = 0;
						}
					}					
					$module[] = $info;
				}
			}
			S('module_all', $module);
		}
		return $module;
	}

	public function checkCanVisit($name){
		$modules = $this->getAll();

		foreach ($modules as $m) {
			if (isset($m['is_setup']) && $m['is_setup'] == 0 && $m['name'] == ucfirst($name)) {
				header("Content-Type: text/html; charset=utf-8");
				exit('您所访问的模块未安装，禁止访问。');
			}
		}
	}

	private function  cleanModulesCache(){
		S('module_all', null);
	}

	public function uninstall($id){
		$module = $this->find($id);
		if ($module['is_setup'] == 0) {
			return array('error_code' => '模块未安装。');
		}
		$uninstallSql = APP_PATH . '/' . $module['name'] . '/Info/uninstall.sql';
		if(file_exists($uninstallSql)){
			$res = $this->executeSqlFile($uninstallSql);
		}else{
			$name = C('DB_PREFIX').strtolower($module['name']);
			$this->execute("DROP TABLE `$name`");
			$res = true;
		}

		if ($res === true) {
			$module['is_setup'] = 0;
			$this->save($module);
		}
		$this->cleanModulesCache();
		return $res;
	}

	public function install($id){
		$module = $this->where(array('id' => $id))->find();
		if ($module['is_setup'] == 1) {
			return array('error_code' => '模块已安装。');
		}
		$uninstallSql = APP_PATH  . $module['name'] . '/Info/install.sql';
		$res = $this->executeSqlFile($uninstallSql);

		if ($res === true) {
			$module['is_setup'] = 1;
			$this->save($module);
		}
		clean_all_cache();//清除全站缓存
		return $res;
	}

	public function getModule($name){
		$module = $this->where(array('name' => $name))->find();
		if (!$module) {
			$m = $this->getInfo($name);
			if ($m['can_uninstall']) {
				$m['is_setup'] = 0;//默认设为已安装，防止已安装的模块反复安装。
			} else {
				$m['is_setup'] = 1;
			}
			$m['id'] = $this->add($m);
			return $m;
		} else {
			return $module;
		}
	}

	private function getInfo($name){
		if (file_exists(APP_PATH . '/' . $name . '/Info/info.php')) {
			$module = require(APP_PATH . '/' . $name . '/Info/info.php');
			return $module;
		} else {
			return array();
		}
	}


	private function getFile($folder){
		//打开目录
		$fp = opendir($folder);
		//阅读目录
		while (false != $file = readdir($fp)) {
			//列出所有文件并去掉'.'和'..'
			if ($file != '.' && $file != '..') {
				$file = "$file";
				$arr_file[] = $file;
			}
		}
		//输出结果
		if (is_array($arr_file)) {
			while (list($key, $value) = each($arr_file)) {
				$files[] = $value;
			}
		}
		//关闭目录
		closedir($fp);
		return $files;
	}

	public function isInstalled($name){
		$module = $this->getModule($name);
		if ($module['is_setup']) {
			return true;
		} else {
			return false;
		}
	}
} 