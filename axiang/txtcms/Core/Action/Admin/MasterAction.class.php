<?php
/**
 * TXTCMS 管理员模块
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class MasterAction extends AdminAction {
	public $Master;
	public function _init(){
		parent::_init();
		$this->Master=DB('master');
	}
	public function index(){
		$data=$this->Master->where('id=1')->find();
		$this->assign($data);
		$this->display();
	}
	public function update(){
		$data=$this->Master->where('id=1')->find();
		$ajax=array();
		if(md5($_POST['pass'])==$data['pass']){
			$values=array();
			$values['name']=htmlspecialchars(trim($_POST['name']));
			$values['pass']=md5($_POST['pass1']);
			$this->Master->where('id=1')->data($values)->save();
			$_SESSION['admin']['id']=$values['name'];
			$ajax['status'] = 1;
		}else{
			$ajax['status'] = 0;
			$ajax['info'] = '旧密码不正确！';
		}
		$this->ajaxReturn($ajax);
	}
}