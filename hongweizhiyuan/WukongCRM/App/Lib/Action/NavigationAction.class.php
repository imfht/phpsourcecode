<?php 
/**
 *
 * 导航菜单相关模块
 *
 **/ 
class NavigationAction extends Action {
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index','settingmenu')
		);
		B('Authenticate', $action);
	}
	
	public function index() {
		if(isset($_GET['postion_top']) || isset($_GET['postion_user']) || isset($_GET['postion_more'])){
			$menu = array();
			$menu['top'] = explode(',', $_GET['postion_top']);
			$menu['user'] = explode(',', $_GET['postion_user']);
			$menu['more'] = explode(',', $_GET['postion_more']);
			$user = M('User');
			$navigation = serialize($menu);
			if($user->where("user_id = %d", session('user_id'))->setField('navigation',$navigation)){
				$this->ajaxReturn('1', L('SAVE_THE_SUCCESS'), 1);
			}else{
				$this->ajaxReturn('0', L('SAVE_THE_FAILURE'), 0);
			}
		} else {
			$user = M('User');
			$navigation = M('Navigation');
			$value = $user->where("user_id = %d", session('user_id'))->getField('navigation');
			$menu = unserialize($value);
						
			$list = $navigation->select();
			foreach($list AS $value) {
				$navigationList[$value['id']] = $value;
			}

			foreach($menu AS $k=>$v) {
				foreach($v AS $kk=>$vv) {
					if (isset($navigationList[$vv])) {
						$menu[$k][$kk] = $navigationList[$vv];
						unset($navigationList[$vv]);
					} else {
						unset($menu[$k][$kk]);
					}
				}
			}
			
			foreach($navigationList AS $value) {
				$menu[$value['postion']][] = $value;
			}
			
			$simple_menu = M('User')->where('user_id = %d', session('user_id'))->getField('simple_menu');
			$this->simple_menu = unserialize($simple_menu);
			$this->postion = $menu;
			$this->alert=parseAlert();
			$this->display();
		}	
	}
	
	public function settingMenu(){
		$menu = array();
		$menu = explode(',', $_GET['menu_select']);
		$user = M('User');
		foreach($menu as $k=>$v){
			switch ($v) {
				case 'business' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('BUSINESS'),'url'=>'index.php?m=business&a=add'); break;
				case 'knowledge' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('KNOWLEDGE'),'url'=>'index.php?m=knowledge&a=add'); break;
				case 'product' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('PRODUCT'),'url'=>'index.php?m=product&a=add'); break;
				case 'customer' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('CUSTOMER'),'url'=>'index.php?m=customer&a=add'); break;
				case 'contacts' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('CONTACTS'),'url'=>'index.php?m=contacts&a=add'); break;
				case 'announcement' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('ANNOUNCEMENT'),'url'=>'index.php?m=announcement&a=add'); break;
				case 'event' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('EVENT'),'url'=>'index.php?m=event&a=add'); break;
				case 'contract' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('CONTRACT'),'url'=>'index.php?m=contract&a=add'); break;
				case 'task' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('TASK'),'url'=>'index.php?m=task&a=add'); break;
				case 'receivables' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('THE_ACCOUNTS_RECEIVABLE'),'url'=>'index.php?m=finance&a=add&t=receivables'); break;
				case 'payables' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('THE_ACCOUNTS_PAYABLE'),'url'=>'index.php?m=finance&a=add&t=payables'); break;
				case 'receivingorder' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('VOUCHER'),'url'=>'index.php?m=finance&a=add&t=receivingorder'); break;
				case 'paymentorder' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('PAYMENT_ORDER'),'url'=>'index.php?m=finance&a=add&t=paymentorder'); break;
				case 'log' : 
					$menu[$k] = array('module'=>$v,'module_name'=>L('LOG'),'url'=>'index.php?m=log&a=mylog_add'); break;
			}
		}
		$navigation = serialize($menu);
		if($user->where("user_id = %d", session('user_id'))->setField('simple_menu',$navigation)){
			$this->ajaxReturn('1', L('SAVE_THE_SUCCESS'), 1);
		}else{
			$this->ajaxReturn('0', L('SAVE_THE_FAILURE'), 0);
		}
	}
	
	public function setting(){
		$navigation = M('Navigation');
		$postion = array();
		$postion['top'] = $navigation->where('postion="top"')->order('listorder asc')->select();
		$postion['more'] = $navigation->where('postion="more"')->order('listorder asc')->select();
		$postion['user'] = $navigation->where('postion="user"')->order('listorder asc')->select();

		$this->postion = $postion;
		$this->alert=parseAlert();
		$this->display();
	}
	
	public function add(){
		if(isset($_POST['title'])){
			$navigation = M('navigation');
			$data = $navigation->create();
			if(trim($data['title']) == ''){
				alert('error',L('PLEASE_FILL_OUT_THE_MENU_NAME'),U('navigation/setting'));
			}
			if(trim($data['url']) == ''){
				alert('error',L('THE_LINK_ADDRESS'),U('navigation/setting'));
			}
			$data['listorder'] = $navigation->where('postion = "%s"', $_POST['postion'])->count();
			if($navigation->add($data)){
				alert('success', L('ADD_A_SUCCESS'), U('navigation/setting'));
			} else{
				alert('error', L('PARAMETER_ERRORS_ADD_FAILURE'), U('navigation/setting'));
			}
		}else{
			$this->display();
		}
	}
	public function edit(){
		if($this->isPost()){
			$navigation = M('navigation');
			$data = $navigation->create();
			$menu = $navigation->where('id = %d', $data['id'])->find();
			if ($data['postion'] != $menu['postion']){
				$navigation->where('postion="%s" and listorder > %d', $menu['postion'], $menu['listorder'])->setDec('listorder');
				$data['listorder'] = $navigation->where('postion = "%s"', $_POST['postion'])->count();
			}
	
			if($navigation->save($data)){
				alert('success',L('MODIFY_THE_SUCCESS'), U('navigation/setting'));
			}else{
				alert('error',L('MODIFY_THE_FAILURE'), U('navigation/setting'));
			}
		} else {
			$navigation = M('navigation');
			$menu = $navigation->where('id=%d',$_GET['id'])->find();
			$this->menu = $menu;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function sort(){	
		if(isset($_GET['postion_top']) || isset($_GET['postion_user']) || isset($_GET['postion_more'])){
			$navigation = M('Navigation');
			
			foreach(explode(',', $_GET['postion_top']) AS $k=>$v) {
				$data = array('id'=> $v, 'listorder'=>$k, 'postion'=>'top');
				$navigation->save($data);
			}
			foreach(explode(',', $_GET['postion_user']) AS $k=>$v) {
				$data = array('id'=> $v, 'listorder'=>$k, 'postion'=>'user');
				$navigation->save($data);
			}
			foreach(explode(',', $_GET['postion_more']) AS $k=>$v) {
				$data = array('id'=> $v, 'listorder'=>$k, 'postion'=>'more');
				$navigation->save($data);
			}
			
			$this->ajaxReturn('1', L('SAVE_THE_SUCCESS'), 1);
		} else{
			$this->ajaxReturn('0', L('SAVE_THE_FAILURE'), 1);
		}
	}
	
	
	public function delete(){
		$navigation = M('Navigation');
		if($_POST['list']){
			if($navigation->where('id in (%s)', implode(',', $_POST['list']))->delete()){
				
				$postion_top = $navigation->where('postion="top"')->order('listorder asc')->field('id')->select();
				foreach($postion_top AS $k=>$v) {
					$data = array('id'=> $v['id'], 'listorder'=>$k, 'postion'=>'top');
					$navigation->save($data);
				}
				$postion_more = $navigation->where('postion="more"')->order('listorder asc')->field('id')->select();
				foreach($postion_more AS $k=>$v) {
					$data = array('id'=> $v['id'], 'listorder'=>$k, 'postion'=>'more');
					$navigation->save($data);
				}
				$postion_user = $navigation->where('postion="user"')->order('listorder asc')->field('id')->select();
				foreach($postion_user AS $k=>$v) {
					$data = array('id'=> $v['id'], 'listorder'=>$k, 'postion'=>'user');
					$navigation->save($data);
				}
				alert('success', L('DELETED SUCCESSFULLY'),U('navigation/setting'));
			}else{
				$this->error(L('DELETE FAILED CONTACT THE ADMINISTRATOR'));
			}
		}else{
			alert('error', L('UNCHECK_ANY_MENU'),U('navigation/setting'));
		}
	}
}