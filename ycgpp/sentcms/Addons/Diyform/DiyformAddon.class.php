<?php
namespace Addons\Diyform;
use Common\Controller\Addon;
class DiyformAddon extends Addon{

	public $info = array(
		'name'	=>	'Diyform',
		'title'	=>	'自定义表单插件',
		'description'=>'自定义表单插件',
		'status'=>1,
		'author'=>'Colin',
		'version'=>'0.1',
	);

	public $admin_list = array(
		'list_grid' => array(
			'id:ID',
			'title:名称',
			'table:表名',
			'create_time:创建时间',
			'id:操作:index.php?s=/home/addons/execute/_addons/Diyform/_controller/Diyform/_action/showform/id/,发布|admin.php?s=/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/dataManage/id/,管理|admin.php?s=/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/listfield/id/,字段|admin.php?s=/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/edit/id/,编辑|admin.php?s=/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/delete/id/,删除',
		),
		'model'=>'Diyform',
		'order'=>'id asc'
	);

	public $custom_adminlist = '';

	public function install(){
		$this->getisHook('Diyform',$this->info['name'],$this->info['description']);
		$sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/install.sql');
        $sqlFormat = $this->sql_split($sqldata, C('DB_PREFIX'));
        $counts = count($sqlFormat);
         
        for ($i = 0; $i < $counts; $i++) {
            $sql = trim($sqlFormat[$i]);
            D()->execute($sql);
        }
		return true;
	}

	public function uninstall(){
		$sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/uninstall.sql');
        $sqlFormat = $this->sql_split($sqldata, C('DB_PREFIX'));
        $counts = count($sqlFormat);
         
        for ($i = 0; $i < $counts; $i++) {
            $sql = trim($sqlFormat[$i]);
            D()->execute($sql);
        }
		return true;
	}

	public function Diyform($name){
		$this->display('View/Default/'.$name);
	}


}