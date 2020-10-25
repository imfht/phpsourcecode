<?php
/*
* @copyright (c) 2012-3000 IKPHP All Rights Reserved
* @author 小麦 修改时间 2014年3月16日 2点47分
* @Email:810578553@qq.com
* @IKPHP所有应用管理
*/
namespace Admin\Controller;
use Common\Controller\BackendController;

class AppsController extends BackendController {
	
	private $appStatus = array('0'=>'关闭','1'=>'开启');	//应用状态
	private $host_type_alias = array(0=>'本地应用',1=>'远程应用');	//托管状态
	
	public function _initialize() {
		parent::_initialize ();
		
		$this->app_mod = D ( 'Common/App' );
	}
	public function doaction(){
		$ik = $this->_get('ik','trim','');
		if($ik){
			switch ($ik) {
				case "editapp" :
					$this->preinstall();
					break;
				case "setstatus" :
					$this->setstatus();
					break;
				case "uninstallapp" :
					$this->uninstallapp();
					break;
			}
		}else{
			$this->error('无效的参数！');
		}
	}
	// 卸载app
	public function uninstallapp(){
		$app_id = $this->_get('id','trim,intval','0');
		if($app_id>0 && $strApp = $this->app_mod->getOneApp($app_id)){
			
			$status = $this->app_mod->uninstallApp($app_id);
			if($status === true){
				$this->success('卸载成功！');
			}else{
				$this->error($status);
			}
		}else {
			$this->error('不存在该应用');
		}
	}
	// 设置状态 开启还是关闭
	public function setstatus(){
		$app_id = $this->_get('id','trim,intval','0');
		$status = $this->_get('status','trim,intval','0');
		if($app_id>0 && $strApp = $this->app_mod->getOneApp($app_id)){
			$this->app_mod->where(array('app_id'=>$app_id))->setField('status',$status);
			$this->success('操作成功');
		}else {
			$this->error('不存在该应用');
		}
	}
	// 未安装的应用
	public function uninstall(){
		
		$appList = $this->app_mod->getUninstallList(); 
		foreach($appList as &$v){
			$v['host_type_alias'] = $this->host_type_alias[$v['host_type']];
			!empty($v['author_url']) && $v['author_name'] = "<a href='{$v['author_url']}'>{$v['author_name']}</a>";  
			$v['icon_url'] = empty($v['icon_url']) ? '<img src="'.APP_PATH.$v['app_name'].'/Appinfo/icon_app.png" >' : "<img src='{$v['icon_url']}'>";
			$v['doaction'] =  "<a href='".U('admin/apps/preinstall',array('app_name'=>$v['app_name'],'install'=>1))."'>安装</a>";				
		}
		$this->assign('list',$appList);
		
		$this->title ( '未安装的应用' );
		$this->display();
	}
	// 已经安装的应用
	public function installed(){
		$appList = $this->app_mod->order('setuptime desc')->select();
		foreach ($appList as $key=>$item){
			$list[] = $item;
			$list[$key]['is_nav'] = $item['is_nav'] == 0 ? '否' : '是' ;
			$list[$key]['icon_url'] = empty($item['icon_url']) ? '<img src="'.APP_PATH.$item['app_name'].'/Appinfo/icon_app.png" >' : "<img src='{$item['icon_url']}'>";
		}
		$this->assign('list', $list);
		$this->title ( '已安装的应用' );
		$this->display();
	}
	// 预安装
	public function preinstall(){
		$install = $this->_get('install','trim,intval');
		$app_name = $this->_get('app_name','trim');
		!$app_name && $this->error("无法找到此应用"); 
		
		if(!empty($install)){
			$strApp = $this->app_mod->__getAppInfo($app_name);
			$strApp['status'] = 1;
			$strApp['is_nav'] = 1;
			$strApp['display_order'] = 0;
			$strApp['is_edit'] = 0;
			$this->title ( '安装应用' );
		}else{
			$map['app_name']  = $app_name;
			$strApp = $this->app_mod->where($map)->find();
			$strApp['is_edit'] = 1;
			$this->title ( '编辑应用' );
		}
		$this->assign('strApp',$strApp);
		$this->display('preinstall');
	}
	//更新 安装 应用
	public function saveapp(){
		if(empty($_POST['app_name']) || empty($_POST['app_alias']) || empty($_POST['app_entry'])){
			$this->error('操作失败，必填项不能为空');
		}

		if(IS_POST){ 
			$status = $this->app_mod->saveApp($_POST);
			if($status === true){
				//新版本不需要重新在更新配置文件了
/*				$config_file = CONF_PATH . 'config.php';
				$config = require CONF_PATH . 'config.php';
				$new_config = array(
						'MODULE_ALLOW_LIST' => array($config['MODULE_ALLOW_LIST'],$_POST['app_name']),
				);
				$this->update_config($new_config, $config_file);*/
				
				if(intval($_POST['is_edit']) == 1){
					$this->success('编辑['.$_POST['app_alias'].']成功 ',U('admin/apps/installed'));
				}else{
					$this->success('安装['.$_POST['app_alias'].']成功；记得按F5 刷新下后台才可以看见安装的应用！ ',U('admin/apps/uninstall'));
				}

			}else{
				$this->error($status);
			}
		}
	}
	//官方应用
	public function ikwebsite(){
		echo "下期发布";
	}
	
	
}