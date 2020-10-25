<?php
/*
* @copyright (c) 2012-3000 IKPHP All Rights Reserved
* @author 小麦 改版时间 2014年3月16日 02:55
* @Email:810578553@qq.com
* @IKPHP 第三方应用管理
*/
namespace Admin\Controller;
use Common\Controller\BackendController;

class OauthController extends BackendController {
	public function _initialize() {
		parent::_initialize (); 
		$this->mod = D ( 'Common/Oauth' );
	}
	//管理
	public function manage(){
		$ik = $this->_get ( 'ik', 'trim','unitelogin');

		$this->assign('ik', $ik);
		$this->title ( '第三方应用' );
		switch ($ik) {
			case "unitelogin" :
				$this->unitelogin();
				break;
		}
	}
	//编辑
	public function edit(){
		$ik = $this->_get ( 'ik', 'trim','unitelogin');
	
		$this->assign('ik', $ik);
		$this->title ( '第三方应用' );
		switch ($ik) {
			case "unitelogin" :
				$id = $this->_get('id');
				$strData = $this->mod->where(array('id'=>$id))->find();
				$config = unserialize($strData['config']);
				$strData['key'] = $config['app_key'];
				$strData['keycode'] = $config['app_secret'];
				$this->assign('strData',$strData);
				$this->display('unitelogin_edit');
				break;
		}
	}
	//保存
	public function save(){
		$ik = $this->_get ( 'ik', 'trim','unitelogin');
		switch ($ik) {
			case "unitelogin" :
				$id = $this->_post('id');
				$name = $this->_post('name');
				$key = $this->_post('key');
				$app_secret = $this->_post('keycode');
				$config = serialize(array('app_key'=>$key,'app_secret'=>$app_secret));
				$data = array('name'=>$name,'config'=>$config);
				$this->mod->where(array('id'=>$id))->save($data);
				$this->redirect ( 'oauth/manage',array('ik'=>'unitelogin'));
				break;
		}
	}
	//联合登录
	public function unitelogin(){
		$list = $this->mod->order('id asc')->select();
		foreach ($list as $key=>$item){
			$config = unserialize($item['config']);
			$list[$key]['key'] = $config['app_key'];
			$list[$key]['keycode'] = $config['app_secret'];
		}
		$this->assign('list',$list);
		$this->display('unitelogin');
	}
	//状态
	public function status(){
		$ik = $this->_get ( 'ik', 'trim');
		$id = $this->_get ( 'id', 'intval');
		$status = $this->_get('status','intval','0');
		switch ($ik) {
			case "unitelogin" :
				$this->mod->where(array('id'=>$id))->setField(array('status'=>$status));
				$status = $status == 0? 1 : 0;
				$this->redirect ( 'oauth/manage',array('ik'=>'unitelogin','isaudit'=>$status));
				break;
		}
		 
	}
}