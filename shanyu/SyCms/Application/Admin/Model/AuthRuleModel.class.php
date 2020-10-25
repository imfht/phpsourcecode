<?php
namespace Admin\Model;
use Think\Model;
class AuthRuleModel extends Model{
    protected $_validate = array(
        //array('name','','该规则已存在',1,'unique',1),
        array('title','require','请填写菜单名称',1,'regex',3),
    );


    public function getParent(){
        $where['status']=1;
        $where['type']=array('IN','2,3');
        $list=$this->where($where)->getField('id,pid,title');
        $list=\Lib\ArrayTree::listLevel($list);
        $list_root=array('id'=>0,'title'=>'顶级菜单','level'=>0,'mark'=>'');
        array_unshift($list, $list_root);
        return $list;
    }


	public function buildNode(){
    	//清空节点表
    	$this->execute('TRUNCATE TABLE `'.$this->trueTableName.'`');
    	$node=$this->getAppNode();
    	M('AuthRule')->addAll($node);
    	return true;
	}

    public function getAppNode(){

    	//遍历获取模块
        $modules=$this->getModuleName();

        //获取公用方法
        $Com=new \Common\Controller\CommonController;
        $common=get_class_methods($Com);

        $result=array();
        foreach ($modules as $module) {
        	$controllers=$this->getControllerName($module);
        	foreach ($controllers as $controller) {
        		$actions=$this->getActionName($module,$controller,$common);
                foreach ($actions as $action) {
                    $result[]=array(
                        'name'=>$module.'/'.$controller.'/'.$action,
                        'title'=>L($module.'/'.$controller.'/'.$action),
                    );
                }
        	}
        }
        return $result;
    }

    private function getModuleName(){
        $dirs=glob(APP_PATH.'*');
        $deny=array('Runtime','Common');
        $result=array();
        foreach ($dirs as $dir) {
        	if(!is_dir($dir)) continue;
        	if(in_array(basename($dir),$deny)) continue;
            $result[]=basename($dir);
        }
        return $result; 
    }

    private function getControllerName($module_name){

        $module=APP_PATH.$module_name.'/Controller';
        $files=glob($module.'/*Controller.class.php');
        $result=array();
        foreach ($files as $file) {
            $result[]=strchr(basename($file),'Controller.class.php',true);
        }
        return $result; 
    }
    private function getActionName($module_name,$controller_name,$remove=array()){
        if(!empty($remove) && is_array($remove)){
            return array_diff(get_class_methods(A($module_name.'/'.$controller_name)),$remove);
        }else{
            return get_class_methods(A($module_name.'/'.$controller_name));
        } 
    }

    protected function _after_insert($data,$options){
        session('admin_menu',NULL);
        F('AdminAuth',NULL);
    }
    protected function _after_update($data,$options){
        session('admin_menu',NULL);
        F('AdminAuth',NULL);
    }
    protected function _after_delete($data,$options) {
        session('admin_menu',NULL);
        F('AdminAuth',NULL);
    }

}