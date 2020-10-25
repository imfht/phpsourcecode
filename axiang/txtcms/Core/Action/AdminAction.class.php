<?php
class AdminAction extends TxtcmsAction{
	public function _init(){
		parent::_init();
		//检查登录
		if(!$_SESSION['admin']['id'] && strtolower(MODULE_NAME)!='login' ) {
			$this->success('对不起,您还没有登录,请先登录！',url('Admin/Login/index'));
			exit;
		}
		$data=array();
		$this->tplConf('tpl_path',APP_ROOT.'static/');
		$this->tplConf('template_dir',APP_ROOT.'static/Admin/');
		$this->tplConf('compile_check',true);
		$this->tplConf('caching',false);
		$data['adminid']=$_SESSION['admin']['id'];
		$this->assign($data);
	}
}