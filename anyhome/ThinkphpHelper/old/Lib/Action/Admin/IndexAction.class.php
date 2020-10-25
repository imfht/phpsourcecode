<?php
class IndexAction extends CommonAction {
	public function index()
	{
		$map = $this->_search();
            if ( method_exists( $this, '_filter' ) ) {
            $map = $this->_filter();
        }
        $model = D('Apps');
            if ( !empty( $model ) ) {
            $this->_list( $model, $map );
        }
        cookie( '_currentUrl_', __SELF__ );
        $this->display();
        return;
	}

	public function createApp($appid = 0)
	{
		$Apps = M('Apps');
		$app = $Apps->find($appid);
		if (!$app) {
			afterNote('不存在的应用，或选择错误');
            redirect(cookie( '_currentUrl_' ));
		}
		import('@.ORG.File');
		// File::copy_dir(THINK_PATH,$app['sitePath'].'/PHP');
		File::copy_dir('./Conf',$app['sitePath'].'/Conf');
		File::copy_dir('./Common',$app['sitePath'].'/Common');
		File::copy_dir('./Lib',$app['sitePath'].'/Lib');
		// File::copy_dir('./Public',$app['sitePath'].'/Public');
		// File::copy_dir('./Tpl',$app['sitePath'].'/Tpl');
		// copy("./index.php",$app['sitePath'].'/index.php');
		// afterNote('不存在的应用，或选择错误');
        // redirect(cookie( '_currentUrl_' ));
	}

	public function initApp()
	{
		$this->cpCommonFiles();
		// $this->initTable();
		// $this->initRole();
		// $this->initModule();
		if ($autoCopyCore) {
			# code...
		}
	}

	//复制 公共文件 等之类的
	public function cpCommonFiles()
	{
        $appConfig = F('appConfig');
        $path = "./Lib/Action/Admin/";
        $paths = scandir($path);
        foreach($paths as $file){//遍历
			$file_location= $path."/".$file;//生成路径
			if ($file == "AdvTablesAction.class.php") continue;
			if ($file == "AdvCommonAction.class.php") continue;
			if($file!="." &&$file!=".."){ //判断是不是文件夹
				$this->xcopy($file_location, $appConfig['sitePath'].'/Lib/Action/Admin/'.$file);
			}
		}


		$path = "./Lib/Model/";
        $paths = scandir($path);
        foreach($paths as $file){//遍历
			$file_location= $path."/".$file;//生成路径
			if ($file == "AdvTablesModel.class.php") continue;
			if($file!="." &&$file!=".."){ //判断是不是文件夹
				$this->xcopy($file_location, $appConfig['sitePath'].'/Lib/Model/'.$file);
			}
		}

		$path = "./Tpl/Admin/Public/";
        $paths = scandir($path);
        foreach($paths as $file){//遍历
			$file_location= $path."/".$file;//生成路径
			if ($file == "adv_base.html") continue;
			if ($file == "adv_top.html") continue;
			if($file!="." &&$file!=".."){ //判断是不是文件夹
				$this->xcopy($file_location, $appConfig['sitePath'].'/Tpl/Admin/Public/'.$file);
			}
		}

		$path = "./Tpl/Admin/Widget/";
        $paths = scandir($path);
        foreach($paths as $file){//遍历
			$file_location= $path."/".$file;//生成路径
			if ($file == "adv_base.html") continue;
			if ($file == "adv_top.html") continue;
			if($file!="." &&$file!=".."){ //判断是不是文件夹
				$this->xcopy($file_location, $appConfig['sitePath'].'/Tpl/Admin/Widget/'.$file);
			}
		}
		$this->xcopy('./Common/common.php', $appConfig['sitePath'].'/Common/common.php');
	}

	public function xcopy($source, $dest)
	{
		$acPath = dirname($dest);
		if (!file_exists($dest)) {
            if (!is_dir($acPath))  mkdir($acPath,0777,true);
            copy($source, $dest);
        }
	}

	//初始化所有表
	public function initTable($value='')
	{
		$dbPrefix = C('DB_PREFIX');
		$content = $this->fetch('AdvTables:inittable'); 
		$sql = str_replace("_DBPREFIX_", $dbPrefix, $content);
		$sql_arr = explode(";", $sql);
		foreach ($sql_arr as $k) {
			$Model = new Model();
			$Model->execute($k);
		}
		
	}


	//初始化角色、权限表
	public function initRole($value='')
	{
		$dbPrefix = C('DB_PREFIX');
		$content = $this->fetch('AdvTables:tb_role'); 
		$sql = str_replace("_DBPREFIX_", $dbPrefix, $content);
		$sql_arr = explode(";", $sql);
		foreach ($sql_arr as $k) {
			$Model = new Model();
			$Model->execute($k);
		}
		
	}

	public function initModule($value='')
	{
		$dbPrefix = C('DB_PREFIX');
		$content = $this->fetch('AdvTables:tb_module'); 
		$sql = str_replace("_DBPREFIX_", $dbPrefix, $content);
		$sql_arr = explode(";", $sql);
		foreach ($sql_arr as $k) {
			$Model = new Model();
			$Model->execute($k);
		}
		
	}

}