<?php
class indexController extends baseController{
	protected $layout = 'layout';
	protected $lockFile = '';
	
	protected function init(){
		$this->lockFile = BASE_PATH . 'apps/' . APP_NAME .'/install.lock';
		if(ACTION_NAME !=='ok' && file_exists($this->lockFile) ){
			$this->alert('程序安装已被锁定，如需重新安装，请先删除文件' . str_replace("\\", "/", $this->lockFile));
			exit;
		}
		$this->title = config('title');
		$this->menu = array(
				'index'=>'1.协议',
				'env'=>'2.系统检查',
				'db'=>'3.数据库安装',
				'ok'=>'4.安装状态',
			);
	}
	
	//引导首页
	public function index(){
		$this->display();
	}
	
	//检查环境
	public function env(){
		$this->ifMysql = function_exists('mysql_connect');
		$this->ifVer = ((float)substr(PHP_VERSION, 0, 3) >= 5.0 ) ? true : false;
		$this->ifGd = function_exists('gd_info');
        $this->yes='<span class="green">√</span>';
        $this->no='<span class="red">×</span>';
		
		$rwFiles = array();
		foreach((array)config('rw_files') as $file){
			$perms = substr( sprintf("%o", @fileperms($file)), -4);
			$rwFiles[$file] = $perms >0644 ? true : false;
		}
		$this->rwFiles = $rwFiles;
		
		$this->display();
	}
	
	//安装数据库
	public function db(){
		if( !$this->isPost() ){
			$this->display();
		}else{
			config('DB', $_POST);
			
			//安装数据库文件
			model('install')->installSql( BASE_PATH . 'apps/' . APP_NAME .'/install.sql' );

			//修改配置文件
			if( !save_config(BASE_PATH . '/conf/config.php', array('DB' => config('DB'),'appID' => getcode(8) ) ) ){
				cpError::show('配置文件写入失败！');
			}
			
			//安装成功，创建锁定文件
			if( NULL == ($fp = @fopen($this->lockFile, 'w')) ){
				cpError::show('数据库安装成功，但创建锁定文件失败！请手动删除install安装目录');
			}
			
			$this->redirect( url('index/ok') );
		}
	}
	
	//安装成功
	public function ok(){
		$this->display();
		//程序安装结束之后，/删除install目录
		if( config('run_after_del') ){
			del_dir( BASE_PATH . 'apps/' . APP_NAME  );
		}
	}
}