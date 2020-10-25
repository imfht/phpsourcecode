<?php 
/**
 * User Related
 * 用户相关模块
 *
 **/ 

class UserAction extends Action {

	public function _initialize(){
		$action = array(
			'permission'=>array('login','lostpw','resetpw','active','weixinbinding','notice'),
			'allow'=>array('logout','role_ajax_add','getrolebydepartment','dialoginfo','edit', 'listdialog', 'mutilistdialog', 'getrolelist', 'getpositionlist',  'weixin','changecontent')
		);
		B('Authenticate', $action);
	}
	//注册
	/*
	public function register() {
		$user = D('User');
		if($_GET['op'] == 'checkname'){
			$this->ajaxReturn(0, "用户名可以使用！",0); 
			
			if($user->where('name = "%s"', $_GET['name'])->find()){ 
				$this->ajaxReturn(1, "用户名不可以使用！",1);
			}else{ 
				$this->ajaxReturn(0, "用户名可以使用！"); 
			}
		}else{
			if (isset($_POST['name']) && $_POST['name'] != '') { 	
				if ($user->create()) {
					if ($user->add()) {					
						$this->success('恭喜，添加会员成功！');
					} else {
						$this->error('注册失败，请联系管理员！');
					}
				} else {	
					exit($user->getError());			
				}
			}else{
				$category = M('user_category');
				$this->categoryList = $category->select();
				
				$this->display();
				
			}
		}
	}*/
	//登录
	public function login() {
		$m_announcement = M('announcement');
		$where['status'] = array('eq', 1);
		$where['isshow'] = array('eq', 1);
		$this->announcement_list = $m_announcement->where($where)->order('order_id')->select();
		if (session('?name')){
			$this->redirect('index/index',array(), 0, '');
		}elseif($_POST['submit']){
			$code = $_POST['check_code'];
			if(!isset($code) || $code ==''){
				alert('error', L('INVALIDATE_CHECK_CODE'),$_SERVER['HTTP_REFERER']); 
			}
			if(($code)!=$_SESSION['code_char'])
			{
				alert('error',L('ERROR_CHECK_CODE'),$_SERVER['HTTP_REFERER']);
			}
			if((!isset($_POST['name']) || $_POST['name'] =='')||(!isset($_POST['password']) || $_POST['password'] =='')){
				alert('error', L('INVALIDATE_USER_NAME_OR_PASSWORD')); 
			}elseif (isset($_POST['name']) && $_POST['name'] != ''){
				$m_user = M('user');
				$user = $m_user->where(array('name' => trim($_POST['name'])))->find();
				
				if ($user['password'] == md5(md5(trim($_POST['password'])) . $user['salt'])) {				
					if (-1 == $user['status']) {
						alert('error', L('YOU_ACCOUNT_IS_UNAUDITED'));
					} elseif (0 == $user['status']) {
						alert('error', L('YOU_ACCOUNT_IS_AUDITEDING'));
					}elseif (2 == $user['status']) {
						alert('error', L('YOU_ACCOUNT_IS_DISABLE'));
					}else {
						$d_role = D('RoleView');
						$role = $d_role->where('user.user_id = %d', $user['user_id'])->find();
						if ($_POST['autologin'] == 'on') {
							session(array('expire'=>259200));
							cookie('user_id',$user['user_id'],259200);
							cookie('name',$user['name'],259200);
							cookie('salt_code',md5(md5($user['user_id'] . $user['name']).$user['salt']),259200);
						}else{
							session(array('expire'=>3600));
						}
						if (!is_array($role) || empty($role)) {
							alert('error', L('HAVE_NO_POSITION')); 
						} else {
							if($user['category_id'] == 1){
								session('admin', 1);
							}
							session('role_id', $role['role_id']);
							session('position_id', $role['position_id']);
							session('role_name', $role['role_name']);
							session('department_id', $role['department_id']);
							session('name', $user['name']);
							session('user_id', $user['user_id']);
							alert('success', L('LOGIN_SUCCESS'), U('Index/index'));		
						}
					}
				} else {
					alert('error', L('INCORRECT_USER_NAME_OR_PASSWORD'),$_SERVER['HTTP_REFERER']); 				
				}
			}			
			$this->alert = parseAlert();
			$this->display();
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	//找回密码
	public function lostpw() {
		if($_POST['submit']){
			if ($_POST['name'] || $_POST['email']){
				$user = M('User');
				if ($_POST['name']){
					$info = $user->where('name = "%s"',trim($_POST['name']))->find();
					if(!isset($info) || $info == null){
						$this->error(L('NOT_FIND_USER_NAME'));
					}
				} elseif ($_POST['email']){
					$info = $user->where('email = "%s"',trim($_POST['email']))->find();
					if (ereg('^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+.[a-zA-Z]{2,3}$',$_POST['email'])){
						if (!isset($info) || $info == null){
							$this->error(L('EMAIL_NOT_BE_USEED'));
						}
					}else{
						$this->error(L('INVALIDATE_EMAIL'));
					}					
				}				
				$time = time();
				
				// 手动进行令牌验证
				if (!$user->autoCheckToken($_POST)){		
					$this->error(L('FORM_REPEAT_SUBMIT'), U('user/login'));
				}
				
				$user->where('user_id = ' . $info['user_id'])->save(array('lostpw_time' => $time));
				$verify_code = md5(md5($time) . $info['salt']);
				C(F('smtp'),'smtp');
				import('@.ORG.Mail');
				$url = U('user/resetpw', array('user_id'=>$info['user_id'], 'verify_code'=>$verify_code),'','',true);
				$content = L('FIND_PASSWORD_EMAIL' ,array($_POST['name'] , $url));
				if (SendMail($info['email'],L('FIND_PASSWORD_LINK'),$content,L('5KCRM_ADMIN'))){
					$this->success(L('SEND_FIND_PASSWORD_EMAIL_SUCCESS'));
				}
			} else {
				$this->error(L('INPUT_USER_NAME_OR_EMAIL'));
			}
		} else{
			if (!F('smtp')) {
				$this->error(L('CAN_NOT_USER_THIS_FUNCTION_FOR_NOT_SET_SMTP'));
			}
			$this->alert = parseAlert();
			$this->display();			
		}
	}
	//密码重置
	public function resetpw(){
		$verify_code = trim($_REQUEST['verify_code']);
		$user_id = intval($_REQUEST['user_id']);
		$m_user = M('User');
		$user = $m_user->where('user_id = %d', $user_id)->find();
		
		// 手动进行令牌验证
		if (!$m_user->autoCheckToken($_POST)){		
			$this->error(L('FORM_REPEAT_SUBMIT'), U('user/login'));
		}
		if (is_array($user) && !empty($user)) {
			if ((time()-$user['lostpw_time'])>86400){
				alert('error', L('LINK_DISABLE_PLEASE_FIND_PASSWORD_AGAIN'),U('user/lostpw'));
			}elseif (md5(md5($user['lostpw_time']) . $user['salt']) == $verify_code) {
				if ($_REQUEST['password']) {
					$password = md5(md5(trim($_REQUEST["password"])) . $user['salt']);
					$m_user->where('user_id =' . $_REQUEST['user_id'])->save(array('password'=>$password, 'lostpw_time'=>0));
					alert('success', L('EDIT_PASSWORD_SUCCESS_PLEASE_LOGIN'), U('user/login'));
				} else {
					$this->alert = parseAlert();
					$this->display();
				}
			} else{
				$this->error(L('FIND_PASSWORD_LINK_DISABLE'));
			}		
		} else {
			$this->error(L('FIND_PASSWORD_LINK_DISABLE'));
		}
	}
	
	//退出
	public function logout() {
		session(null);
		cookie('user_id',null);
		cookie('name',null);
		cookie('salt_code',null);
		$this->success(L('LOGIN_OUT_SUCCESS'), U('User/login'));
	}
	
	public function listDialog() {
		//1表示所有人  2表示下属
		if($_GET['by'] == 'task'){
			$all_or_below = C('defaultinfo.task_model') == 2 ? 1 : 0;
		}else{
			$all_or_below = $_GET['by'] == 'all' ? 1 : 0;
		}
		$d_role_view = D('RoleView');
		$where = '';
		$all_role = M('role')->where('user_id <> 0')->select();
		$below_role = getSubRole(session('role_id'), $all_role);
		if(!$all_or_below){
			$below_ids[] = session('role_id');
			foreach ($below_role as $key=>$value) {
				$below_ids[] = $value['role_id'];
			}
			$where = 'role.role_id in ('.implode(',', $below_ids).')';
		}
		$where .= ' and user.status = 1';
		$role_list = $this->role_list = $d_role_view->where($where)->select();
		$count =  $d_role_view->where($where)->count();
		$this->count = $count;
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$departments = M('roleDepartment')->select();
		$department_id = M('position')->where('position_id = %d', session('position_id'))->getField('department_id'); 
		$departmentList[] = M('roleDepartment')->where('department_id = %d', $department_id)->find();
		$departmentList = array_merge($departmentList, getSubDepartment($department_id,$departments,''));
		$this->assign('departmentList', $departmentList);
		
		$this->display();
	}
	
	public function mutiListDialog(){
		//1表示所有人  2表示下属
		if($_GET['by'] == 'task'){
			$all_or_below = C('defaultinfo.task_model') == 2 ? 1 : 0;
		}else{
			$all_or_below = $_GET['by'] == 'all' ? 1 : 0;
		}
		$d_role = D('RoleView');
		$sub_role_id = getSubRoleId(false);
		$departments_list = M('roleDepartment')->select();	
		foreach($departments_list as $k=>$v){
			$where = array();
			if(!$all_or_below)
				$where['role_id'] = array('in', $sub_role_id);
			$where['position.department_id'] =  $v['department_id'];
			$roleList = $d_role->where($where)->select();
			$departments_list[$k]['user'] = $roleList;
		}
		$this->departments_list = $departments_list;
		$this->display();
	}
	
	//删除员工
	public function delete(){
alert('error', L('CAN_NOT_DELETE_USER'), U('user/index'));
		$m_user = M('user');
		$r_module = array('Log'=>'RLogUser', 'File'=>'RFileUser');
		if($this->isPost()){
			$user_ids = is_array($_POST['user_id']) ? implode(',', $_POST['user_id']) : '';
			if(in_array(session('user_id'), $_POST['user_id'])) alert('error', L('CAN_NOT_DELETE_YOURSELF'), U('user/index'));

			if ('' == $user_ids) {
				alert('error', L('NOT CHOOSE ANY'), U('user/index'));
			} else {
				if($m_user->where('user_id in (%s) and user_id <> 1 and user_id <> %d', $user_ids, session('user_id'))->delete()){
					if(M('role')->where('user_id in (%s)', $user_ids)->delete()){
						foreach ($_POST['user_id'] as $value) {
							foreach ($r_module as $key2=>$value2) {
								$module_ids = M($value2)->where('user_id = %d', $value)->getField($key2 . '_id', true);
								M($value2)->where('user_id = %d', $value) -> delete();
								if(!is_int($key2)){	
									M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
								}
							}
						}
						alert('success', L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
					} else {
						alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), U('user/index'));
					}
				} else {
					alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), U('user/index'));
				}
			}
		} elseif($_GET['id']) {
			if(session('user_id') == intval($_GET['id'])) alert('error', L('CAN_NOT_DELETE_YOURSELF'), U('user/index'));
			$user = $m_user->where('user_id = %d', $_GET['id'])->find();
			if (is_array($user)) {
				if($m_user->where('user_id = %d and user_id <> 1 and user_id <> %d', $_GET['id'], session('user_id'))->delete()){
					if(M('role')->where('user_id = %d', $_GET['id'])->delete()){
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('user_id = %d', $_GET['id'])->getField($key2 . '_id', true);
							M($value2)->where('user_id = %d', $_GET['id']) -> delete();
							if(!is_int($key2)){
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
						alert('success', L('DELETED SUCCESSFULLY'), U('user/index'));
					} else {
						alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), U('user/index'));
					}
				}else{
					alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), U('user/index'));
				}				
			} else {
				alert('error', L('RECORD NOT EXIST' ,array('')), U('user/index'));
			}			
		} else {
			alert('error', L('SELECT_RECORD_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	//修改自己的信息
	public function edit(){
		if ($this->isPost()) {
            if(!session('?admin') && session('user_id') != $_POST['user_id']){
                alert('error',L('YOU_DO_NOT_HAVE_THIS_RIGHT'),$_SERVER['HTTP_REFERER']);
            }
			if (!ereg('^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$', $_POST['email'])){
				alert('error', L('INVALIDATE_EMAIL'), $_SERVER['HTTP_REFERER']);
			}
            if (!ereg('^1[358][0-9]{9}$', $_POST['telephone'])){
				alert('error', L('INVALIDATE_TELEPHONE'), $_SERVER['HTTP_REFERER']);
			}
            $m_user = M('user');
			$m_role = M('role');
			$user=M('user')->where('user_id = %d', $_POST['user_id'])->find();
			//编辑头像
			if (isset($_FILES['img']['size']) && $_FILES['img']['size'] > 0) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error',L("ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE"),U('user/edit'));
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error',$upload->getErrorMsg(),U('user/edit'));
				}else{
					$info =  $upload->getUploadFileInfo();
				}
				if(is_array($info[0]) && !empty($info[0])){
					$upload = $dirname . $info[0]['savename'];
				}else{
					alert('error',L('LOGO EDIT FAILED'),U('user/edit'));
				}
			}
			if ($m_user->create()) {
				if(isset($_POST['password']) && $_POST['password']!=''){
					$m_user->password = md5(md5(trim($_POST["password"])) . $user['salt']);
				} else {
					unset($m_user->password);
				}
				$is_update = false;
                if(session('?admin')){
					$is_update = $m_role->where('user_id = %d', $_POST['user_id'])->setField('position_id', $_POST['position_id']);
				}else{
                    unset($m_user->category_id);
                    unset($m_user->name);
                }
				if($upload)
				{
					$m_user->img =$upload;
				}
				else{
					unset($m_user->img);
				}
				if($m_user->save() || $is_update){
					actionLog($_POST['user_id']);
					alert('success',L('EDIT_USER_INFO_SUCCESS'),U('user/index'));
				}else{
					alert('error',L('USER_INFO_NOT_CHANGE'),$_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error',L('EDIT_USER_INFO_FAILED'),$_SERVER['HTTP_REFERER']);
			}
		}else{
			$user_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : session('user_id');
            if(!session('?admin') && session('user_id') != $user_id){
                alert('error',L('YOU_DO_NOT_HAVE_THIS_RIGHT'),$_SERVER['HTTP_REFERER']);
            }
			$d_user = D('RoleView');
			$user = $d_user->where('user.user_id = %d', $user_id)->find();
			$user['category'] = M('user_category')->where('category_id = %d', $user['category_id'])->getField('name');
			$this->categoryList = M('user_category')->select();
			$status_list = array(L('INACITVE'),L('ACITVE'),L('DISABLE'));
			$this->assign('statuslist', $status_list);
			if($user['department_id']){
				$this->position_list = M('position')->where('department_id = %d', $user['department_id'])->select();
			}
			$department_list = getSubDepartment(0, M('role_department')->select());
			$this->assign('department_list', $department_list);
			$this->user = $user;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function dialogInfo(){
		$role_id = intval($_REQUEST['id']);
		$role = D('RoleView')->where('role.role_id = %d', $role_id)->find();
		$user = M('user')->where('user_id = %d', $role['user_id'])->find();
		$user[role] = $role;
		$this->user = $user;
		$this->categoryList = M('user_category')->select();
		$this->alert = parseAlert();
		$this->display();
	}

	
	public function changeContent(){
		if($this->isAjax()){
			$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
            $department_id = $this->_get('department');
			if($_GET['department'] == 'all'){
				$department_id = session('department_id');
			}else{
				$department_id = $this->_get('department');
			}
			$departRoleArr = getRoleByDepartmentId($department_id);
			$departRoleIdArr = array();
			foreach($departRoleArr as $k=>$v){
				$departRoleIdArr[] = $v['role_id'];
			}
			$where['status'] = array('eq', 1);
			if($this->_get('name','trim') == ''){
				$where['role_id'] = array('in', $departRoleIdArr);
				$list = $d_role_view->where($where)->order('role_id')->page($p.',10')->select();
				$data['list'] = $list;
				$count = $d_role_view->where($where)->order('role_id')->count();
			}else{
				$where['user.name'] = array('like', '%'.trim($_GET['name']).'%');
				$list = $d_role_view->where($where)->order('role_id')->page($p.',10')->select();
				$count = $d_role_view->where($where)->order('role_id')->count();
				$data['list'] = $list;
			}
			$data['p'] = $p;
			$data['count'] = $count;
			$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
			$this->ajaxReturn($data, '', 1);
		}
	}
	//添加员工
	/*public function add2(){
		$user = D('User');
		$category = M('user_category');
		$this->categoryList = $category->select();
		if ($_POST['submit']) {
			$this->value = $_POST;
			if(!isset($_POST['name']) || $_POST['name'] == ''){
				alert('error','请输入用户名');				
				$this->alert = parseAlert();
				$this->display();
			}if(!isset($_POST['password']) || $_POST['password'] == ''){
				alert('error', '请输入密码！');
				$this->alert = parseAlert();
				$this->display();
			}elseif(!isset($_POST['repassword']) || $_POST['repassword'] == ''){
				alert('error', '请输入确认密码！');
				$this->alert = parseAlert();
				$this->display();
			}elseif(!isset($_POST['email']) || $_POST['email'] == ''){
				alert('error', '请输入邮箱！');
				$this->alert = parseAlert();
				$this->display();
			}elseif(!isset($_POST['category_id']) || $_POST['category_id'] == ''){
				alert('error', '请选择用户身份!');
				$this->alert = parseAlert();
				$this->display();
			}elseif($_POST['password'] != $_POST['repassword']){
				alert('error', '两次输入密码不一致');
				$this->alert = parseAlert();
				$this->display();
			}else{
				if ($user->create()) {
					if ($user->add()) {
						if($_POST['submit'] == "保存") {
							alert('success', '员工添加成功！', U('user/index'));
						} else {
							alert('success', '员工添加成功！', U('user/add'));
						}
					} else {
						$this->error('添加失败，请联系管理员！');
					}
				} else {
					alert('error',$user->getError());
					$this->alert = parseAlert();
					$this->display();
				}
			}
		}else{
			$role_list = M('role')->select();	
			if (session('?admin')){
				$this->assign('roleList', getSubRole(0, $role_list, ''));
			} else {
				$this->assign('roleList', getSubRole(session('role_id'), $role_list, ''));
			}
			$this->alert = parseAlert();
			$this->display();
		}
	}
	*/
	public function add(){
		$m_role = M('Role');
		$m_user = D('User');
		if ($this->isPost()){
			$m_user->create(); 
			// echo $m_user->name; 
			if($_POST['radio_type'] == 'email'){
				//邮箱激活
				if (!isset($_POST['name']) || $_POST['name'] == '') {
					alert('error', L('INPUT_USER_NAME'), $_SERVER['HTTP_REFERER']);				
				} elseif (!isset($_POST['email']) || $_POST['email'] == ''){
					alert('error', L('INPUT_EMAIL'), $_SERVER['HTTP_REFERER']);	
				} elseif (!ereg('^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$', $_POST['email'])){
					alert('error', L('INVALIDATE_EMAIL'), $_SERVER['HTTP_REFERER']);
				} elseif ($m_user->where('email = "%s"', $_POST['email'])->find()) {
					alert('error', L('EMAIL_HAS_BEEN_BOUND'), $_SERVER['HTTP_REFERER']);
				} elseif (!isset($_POST['category_id']) || $_POST['category_id'] == ''){
					alert('error', L('PLEASE_SELECT_USER_CATEGORY'), $_SERVER['HTTP_REFERER']);
				} elseif (!session('?admin') && intval($_POST['category_id'])==1) {
					alert('error', L('YOU_HAVE_NO_PERMISSION_TO_ADD_ADMIN'), $_SERVER['HTTP_REFERER']);
				} elseif (!isset($_POST['position_id']) || $_POST['position_id'] == ''){
					alert('error', L('SELECT_POSITION_TO_ADD_USER'), $_SERVER['HTTP_REFERER']);
				} elseif ($m_user->where('name = "%s"', $_POST['name'])->find()){
					alert('error', L('USER_EXIST'), $_SERVER['HTTP_REFERER']);
				}
				$m_user->status = 0;
				//为用户设置默认导航（根据系统菜单设置中的位置）
				$m_navigation = M('navigation');
				$navigation_list = $m_navigation->order('listorder asc')->select();
				$menu = array();
				foreach($navigation_list as $val){
					if($val['postion'] == 'top'){
						$menu['top'][] = $val['id'];
					}elseif($val['postion'] == 'user'){
						$menu['user'][] = $val['id'];
					}else{
						$menu['more'][] = $val['id'];
					}
				}
				$navigation = serialize($menu);
				$m_user->navigation = $navigation;
				
				if($re_id = $m_user->add()){
					// echo $m_user->getLastSql();
					// die();  
					$time = time();
					$info = $m_user->where('user_id = %d', $re_id)->find();
					$m_user->where('user_id = %d' . $info['user_id'])->setField('reg_time', $time);
					$verify_code = md5(md5($time) . $info['salt']);
					C(F('smtp'),'smtp');
					import('@.ORG.Mail');
					$url = U('user/active', array('user_id'=>$info['user_id'], 'verify_code'=>$verify_code),'','',true);
					$content = L('ADD_USER_EMAIL_CONENT', array($_POST['name'], $url));
					//echo $info['email'].$content;
					//die();
					if (SendMail($info['email'], L('ADD_USER_INVITATION_FROM_5KCRM'), $content,L('5KCRM_ADMIN'))){
						$data['position_id'] = $_POST['position_id'];
						$data['user_id'] = $re_id;
						if($role_id = $m_role->add($data)){
							$m_user->where('user_id = %d', $re_id)->setField('role_id', $role_id);
							actionLog($re_id);
							alert('success', L('ADD_SUCCESS_WAITING_TO_BE_ACTIVED'), U('user/index'));
						}
					} else {
						alert('error', L('CAN_NOT_SEND_INVITATION_CHECK_SMTP'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('ADDING FAILS CONTACT THE ADMINISTRATOR' ,array('')), $_SERVER['HTTP_REFERER']);
				}
			}else{
				//填写密码
				if (!isset($_POST['name']) || $_POST['name'] == '') {
					alert('error', L('INPUT_USER_NAME'), $_SERVER['HTTP_REFERER']);				
				} elseif (!isset($_POST['password']) || $_POST['password'] == ''){
					alert('error', L('INPUT_PASSWORD'), $_SERVER['HTTP_REFERER']);	
				} elseif (!isset($_POST['category_id']) || $_POST['category_id'] == ''){
					alert('error', L('PLEASE_SELECT_USER_CATEGORY'), $_SERVER['HTTP_REFERER']);
				} elseif (!session('?admin') && intval($_POST['category_id'])==1) {
					alert('error', L('YOU_HAVE_NO_PERMISSION_TO_ADD_ADMIN'), $_SERVER['HTTP_REFERER']);
				} elseif (!isset($_POST['position_id']) || $_POST['position_id'] == ''){
					alert('error', L('SELECT_POSITION_TO_ADD_USER'), $_SERVER['HTTP_REFERER']);
				} elseif ($m_user->where('name = "%s"', $_POST['name'])->find()){
					alert('error', L('USER_EXIST'), $_SERVER['HTTP_REFERER']);
				} elseif (!session('?admin') && intval($_POST['category_id'])==1) {
					alert('error', L('YOU_HAVE_NO_PERMISSION_TO_ADD_ADMIN'), $_SERVER['HTTP_REFERER']);
				}
				
				$m_user->status = 1;
				//为用户设置默认导航（根据系统菜单设置中的位置）
				$m_navigation = M('navigation');
				$navigation_list = $m_navigation->order('listorder asc')->select();
				$menu = array();
				foreach($navigation_list as $val){
					if($val['postion'] == 'top'){
						$menu['top'][] = $val['id'];
					}elseif($val['postion'] == 'user'){
						$menu['user'][] = $val['id'];
					}else{
						$menu['more'][] = $val['id'];
					}
				}
				$navigation = serialize($menu);
				$m_user->navigation = $navigation;
				if($re_id = $m_user->add()){
					$data['position_id'] = $_POST['position_id'];
					$data['user_id'] = $re_id;
					if($role_id = $m_role->add($data)){
						$m_user->where('user_id = %d', $re_id)->setField('role_id', $role_id);
						actionLog($re_id);
						if($_POST['submit'] == L('ADD')){
							alert('success', L('ADD_USER_SUCCESS_USER_CAN_LOGIN_NOW'), U('user/index'));
						}else{
							alert('success', L('ADD_USER_SUCCESS_USER_CAN_LOGIN_NOW'), U('user/add'));
						}
					}
				}else{
					alert('error', L('ADDING FAILS CONTACT THE ADMINISTRATOR' ,array('')),$_SERVER['HTTP_REFERER']);
				}
			}
		} else {
			$m_config = M('Config');
			$category = M('user_category');
			$m_position = M('position');
			if(!session('?admin')){
				$department_list = getSubDepartment2(session('department_id'), M('role_department')->select(), 1);
			}else{
				$department_list =  M('role_department')->select();
			}
			
			$where['department_id'] = session('department_id');
			$position_list = getSubPosition(session('position_id'), $m_position->where($where)->select());

			$position_id_array = array();
			$position_id_array[] = session('position_id');
			foreach($position_list as $k => $v){
				$position_id_array[] = $v['position_id'];
			}
			$where['position_id'] = array('in', implode(',', $position_id_array));
			$role_list = $m_position->where($where)->select();
			
			if(empty($role_list) && !session('?admin')){
				alert('error', L('YOU_HAVE_NO_PERMISSION_TO_ADD_USER'), $_SERVER['HTTP_REFERER']);
			}else{
				if(!$m_config->where('name = "smtp"')->find())
				alert('error', L('PLEASE_SET_SMTP_FIRST_TO_INVITATION_USER',array(U('setting/smtp'))));
				$this->categoryList = $category->select();
				$this->assign('department_list', $department_list);
				$this->alert = parseAlert();
				$this->display();
			}
		}
	}
	
	public function getPositionList() {
		if($_GET[id]){
			$m_position = M('position');
			$where['department_id'] = $_GET['id'];
			$position_list = getSubPosition(session('position_id'), $m_position->where($where)->select());

			$position_id_array = array();
			foreach($position_list as $k => $v){
				$position_id_array[] = $v['position_id'];
			}
			if(!session('?admin')){
				$where['position_id'] = array('in', implode(',', $position_id_array));
			}
			$role_list = $m_position->where($where)->select();
			$this->ajaxReturn($role_list, L('GET_SUCCESS'), 1);
		}else{
			$this->ajaxReturn($role_list, L('SELECT_DEPARTMENT_FIRST'), 0);
		}
		
	}
	
	
	public function active() {
		$verify_code = trim($_REQUEST['verify_code']);
		$user_id = intval($_REQUEST['user_id']);
		$m_user = M('User');
		$user = $m_user->where('user_id = %d', $user_id)->find();
		if (is_array($user) && !empty($user)) {
			if (md5(md5($user['reg_time']) . $user['salt']) == $verify_code) {
				if ($_REQUEST['password']) {
					$password = md5(md5(trim($_REQUEST["password"])) . $user['salt']);
					$m_user->where('user_id =' . $_REQUEST['user_id'])->save(array('password'=>$password,'status'=>1, 'reg_time'=>time(), 'reg_ip'=>get_client_ip()));
					alert('success', L('SET_PASSWORD_SUCCESS_PLEASE_LOGIN'), U('user/login'));
				} else {
					$this->alert = parseAlert();
					$this->display();
				}
			} else {
				$this->error(L('FIND_PASSWORD_LINK_DISABLE'));
			}
		} else {
			$this->error(L('FIND_PASSWORD_LINK_DISABLE'));
		}
	}
	
	public function view(){
		if($this->isGet()){
			$user_id = isset($_GET['id']) ? $_GET['id'] : 0;
			$d_user = D('RoleView');
			$user = $d_user->where('user.user_id = %d', $user_id)->find();

			$log_ids = M('rLogUser')->where('user_id = %d', $user_id)->getField('log_id', true);
			$user['log'] = M('log')->where('log_id in (%s)', implode(',', $log_ids))->select();
			$log_count = 0;
			foreach ($user['log'] as $key=>$value) {
				$user['log'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
				$log_count++;
			}
			$user['log_count'] = $log_count;
			
			$file_ids = M('rFileUser')->where('user_id = %d', $user_id)->getField('file_id', true);
			$user['file'] = M('file')->where('file_id in (%s)', implode(',', $file_ids))->select();
			$file_count = 0;
			foreach ($user['file'] as $key=>$value) {
				$user['file'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
				$file_count++;
			}
			$user['file_count'] = $file_count;
			$this->categoryList = M('UserCategory')->select();
			$this->user = $user;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function index(){
		if(!session('?name') || !session('?user_id')){
			redirect(U('User/login/'), 1, L('PLEASE_LOGIN_FIRSET'));
		}
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$status = isset($_GET['status']) ? intval($_GET['status']) : 1 ;
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$d_user = D('UserView'); // 实例化User对象
		
		if(!session('?admin')) $where['role_id'] = array('in', getSubRoleId(true));
		$where['status'] = $status;
		if($id) $where['category_id'] = $id;
		
		import('@.ORG.Page');// 导入分页类
		$count = $d_user->where($where)->count();
	
		$Page = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$Page->parameter = "id=".$id.'&status=' . $status;
		$show  = $Page->show();// 分页显示输出
		$user_list = $d_user->order('reg_time')->where($where)->page($p.',15')->select();
		$this->assign('user_list',$user_list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		
		$category = M('user_category');
		$this->categoryList = $category->select();
		$this->alert = parseAlert();
		$this->display();
	}
	
	
	//查看部门信息
	public function department(){
		if(!session('?name') || !session('?user_id')){
			redirect(U('User/login/'), 0, L('PLEASE_LOGIN_FIRSET'));
		}elseif(!session('?admin')){
			alert('error',L('YOU_HAVE_NO_PERMISSION'),$_SERVER['HTTP_REFERER']);
		}
		
		$this->assign('tree_code', getSubDepartmentTreeCode(0, 1));
		$this->alert = parseAlert();
		$this->display(); 
	}
	
	//添加部门信息
	public function department_add(){
		if(!session('?name') || !session('?user_id')){
			redirect(U('User/login/'), 0, L('PLEASE_LOGIN_FIRSET'));
		}
		
		if($this->isPost()){
			$department = D('roleDepartment');
			if($department->create()){
				$department->name ? '' :alert('error',L('PLEASE_INPUT_DEPARTMENT_NAME'),$_SERVER['HTTP_REFERER']);
				if($department->add()){
					alert('success',L('ADD_DEPARTMENT_SUCCESS'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error',L('ADD_DEPARTMENT_FAILED_CONTACT_ADMIN'),$_SERVER['HTTP_REFERER']);
				}
			}else{
				alert('error',$department->getError(),$_SERVER['HTTP_REFERER']);
			}
		}else{
			$department = M('roleDepartment');
			$department_list = $department->select();	
			$this->assign('departmentList', getSubDepartment(0,$department_list,''));
			$this->display();
		}
	}
	
	public function department_edit(){
		if(!session('?name') || !session('?user_id')){
			redirect(U('User/login/'), 0, L('PLEASE_LOGIN_FIRSET'));
		}
		
		if($_POST['name']){
			$department = M('roleDepartment');
			$department->create();
			if($department->save($data)){
				alert('success',L('EDIT_DEPARTMENT_SUCCESS'),$_SERVER['HTTP_REFERER']);
			}else{
				alert('error',L('DATA_NOT_CHANGED_EDIT_FAILED'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['id']){
			$department = M('roleDepartment');
			$this->assign('vo',$department->where('department_id=' . $_GET['id'])->find());

			$department_list = $department->select();	
			
			foreach($department_list as $key=>$value){
				if($value['department_id'] == $_GET['id']){
					unset($department_list[$key]);
				}
				if($value['parent_id'] == $_GET['id']){
					unset($department_list[$key]);
				}
			}
			$this->assign('departmentList', getSubDepartment(0,$department_list,''));
			$this->display();
		}else{
			$this->error(L('PARAMETER_ERROR'));
		}
	}
	
	public function department_delete(){
		if(!session('?name') || !session('?user_id')){
			redirect(U('User/login/'), 0, L('PLEASE_LOGIN_FIRSET'));
		}
		$department = M('roleDepartment');
		if($_POST['dList']){
			if(in_array(6,$_POST['dList'],true)){
				$this->error(L('CAN_NOT_DELETE_THE_TOP_DEPARTMENT'));
			}else{
				foreach($_POST['dList'] as $key=>$value){
					
					$name = $department->where('department_id = %d',$value)->getField('name');
					if($department->where('parent_id=%d',$value)->select()){
						alert('error',L('DELETE_SUB_DEPARTMENT_FIRST',array($name)), $_SERVER['HTTP_REFERER']);
					}
					$m_position = M('position');
					if($m_position->where('department_id=%d',$value)->select()){
						alert('error',L('DELETE_SUB_POSITION_FIRST',array($name)), $_SERVER['HTTP_REFERER']);
					}
				}
				if($department->where('department_id in (%s)', join($_POST['dList'],','))->delete()){
					alert('success', L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
				}else{
					$this->error(L('DELETE FAILED CONTACT THE ADMINISTRATOR'));
				}
			}
		}elseif($_GET['id']){
			if(6 == intval($_GET['id'])){
				$this->error(L('CAN_NOT_DELETE_THE_TOP_DEPARTMENT'));
			}
			$department_id = intval($_GET['id']); 
			$name = $department->where('department_id = %d', $department_id)->getField('name');
			if($department->where('parent_id=%d', $department_id)->select()){
				alert('error',L('DELETE_SUB_DEPARTMENT_FIRST',array($name)), $_SERVER['HTTP_REFERER']);
			}
			$m_position = M('position');
			if($m_position->where('department_id=%d', $department_id)->select()){
				alert('error',L('DELETE_SUB_POSITION_FIRST',array($name)), $_SERVER['HTTP_REFERER']);
			}
			if($department->where('department_id = %d', $department_id)->delete()){
				alert('success', L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
			}else{
				$this->error(L('DELETE FAILED CONTACT THE ADMINISTRATOR'));
			}
		}else{
			alert('error', L('SELECT_DEPARTMENT_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function role(){
		if(!session('?name') || !session('?user_id')){
			redirect(U('User/login/'), 0, L('PLEASE_LOGIN_FIRSET'));
		}elseif(!session('?admin')){
			alert('error',L('YOU_HAVE_NO_PERMISSION'),$_SERVER['HTTP_REFERER']);
		}
		// $m_position = M('Position');
		// $m_department = M('RoleDepartment');
		// $departments = $m_department->select();	
		// $this->assign('departmentList', getSubDepartment(0,$departments,''));
		
		// $department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;
		
		// if($department_id){
			// $positionList = $m_position->where('department_id = %d', $department_id)->select();
		// }else{
			// $positionList = $m_position->select();
		// }
		

		// $d_role = D('RoleView');
		// foreach($positionList as $k=>$value){
			// $positionList[$k]['department'] = $m_department->where('department_id = %d', $value['department_id'])->find();
			// $positionList[$k]['user'] = $d_role->where('role.position_id = %d', $value['position_id'])->select();
		// }
		// $this->assign('positionList',$positionList);
		$this->assign('tree_code', getSubPositionTreeCode(0, 1));
		$this->alert=parseAlert();
		$this->display();
	}
	
	public function role_ajax_add(){
		if($_POST['name']){
			$role = D('role');
			if($role->create()){
				$role->name ? '' :alert('error',L('PLEASE_INPUT_POSITION_NAME'),$_SERVER['HTTP_REFERER']);
				if($role_id = $role->add()){
					$role_list = M('role')->select();
					if (session('?admin')) {
						$role_list = getSubRole(0, $role_list, '');
					} else {
						$role_list = getSubRole(session('role_id'), $role_list, '');
					}
					foreach ($role_list as $key=>$value) {
						if ($value['user_id'] == 0) {
							$rs_role[] = $role_list[$key];
						}
					}
				
					$data['role_id'] = $role_id;
					$data['role_list'] = $rs_role;
					$this->ajaxReturn($data,L('SEND_SUCCESS'),1);
				}else{
					$this->ajaxReturn("",L('SEND_FAILED'),0);
				}
			}else{
				$this->ajaxReturn("",L('SEND_FAILED'),0);
			}
		}else{
			$department = M('roleDepartment');
			$department_list = $department->select();	
			$this->assign('departmentList', getSubDepartment(0,$department_list,''));
			$role = M('role');
			$role_list = $role->select();	
			$this->assign('roleList', getSubRole(0,$role_list,''));
			$this->display();
		}
	}
	
	public function role_add(){
		if ($this->isPost()) {
			$d_position = D('Position');
			if($d_position->create()){
				$d_position->name ? '' :alert('error',L('PLEASE_INPUT_POSITION_NAME'),$_SERVER['HTTP_REFERER']);
				if($position_id = $d_position->add()){
					alert('success',L('ADD_POSITION_SUCCESS'),$_SERVER['HTTP_REFERER']);
				}else{
					$this->error(L('ADDING FAILS CONTACT THE ADMINISTRATOR' ,array('')));
				}
			}else{
				$this->error(L('ADDING FAILS CONTACT THE ADMINISTRATOR' ,array('')));
			}
		} else {
			$department_list = M('RoleDepartment')->select();	
			$position_list = M('Position')->select();
			$this->assign('departmentList', getSubDepartment(0,$department_list,''));
			$this->assign('positionList', getSubPosition(0,$position_list,''));
			$this->display();
		}
	}
	
	public function getRoleByDepartment(){
		if($this->isAjax()) {
			$department_id = $_GET['department_id'];
			$roleList = getRoleByDepartmentId($department_id);
			$this->ajaxReturn($roleList, '', 1); 
		}
	}
	
	public function roleEdit(){
		if($_GET['auth']){
			$per['position_id'] = intval($_GET['position_id']);
			$per['name'] = trim($_GET['name']);
			$per['description'] = trim($_GET['description']);
			$per['department_id'] = intval($_GET['department_id']);
			$per['parent_id'] = intval($_GET['parent_id']);
			$m_position = M('Position');
			if($m_position -> create($per)){
				if($m_position->save()){
					$this->ajaxReturn(L('EDIT SUCCESSFULLY'),'info',1);
				}else{
			
					$this->ajaxReturn(L('DATA_NOT_CHANGED_EDIT_FAILED'),'info',1);
				}
			}else{
				$this->ajaxReturn(L('EDIT_FAILED_CONTACT_THE_ADMIN'),'info',1);
			}
		}elseif($_GET['id']){
			$m_position = M('position');
			$department_list = M('RoleDepartment')->select();	
			$position_list = $m_position->select();
			$this->assign('position', $m_position->where('position_id=%d', $_GET['id'])->find());
			$this->assign('departmentList', getSubDepartment(0,$department_list,''));
			$this->assign('positionList', getSubPosition(0,$position_list,''));
			$this->display();
		}else{
			$this->error(L('PARAMETER_ERROR'));
		}
	}
	

	public function role_delete(){
		$m_position = M('position');
		$d_role = D('RoleView');
		if($_POST['roleList']){
			if(in_array(1,$_POST['roleList'],true)){
				$this->error(L('CAN_NOT_DELETE_THE_TOP_PERMISSION_USER'));
			}else{
				foreach($_POST['roleList'] as $key=>$value){
					$name = $m_position->where('role_id = %d', $value)->getField('name');
					if($d_role->where('position_id = %d', $value)->select()){
						alert('error',L('HAVE_USER_ON_THIS_POSITION',array($name)), $_SERVER['HTTP_REFERER']);
					}
				}
				if($m_position->where('role_id in (%s)', join($_POST['roleList'],','))->delete()){
					alert('success', L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
				}else{
					$this->error(L('DELETE FAILED CONTACT THE ADMINISTRATOR'));
				}
			}
		}elseif($_GET['id']){
			if(1 == intval($_GET['id'])){
				$this->error(L('CAN_NOT_DELETE_THE_TOP_PERMISSION_USER'));
			}
			if($d_role->where('position.position_id = %d', intval($_GET['id']))->select()){
				alert('error', L('HAVE_USER_ON_THIS_POSITION',array($name)), $_SERVER['HTTP_REFERER']);
			}else{
				if($m_position->where('position_id = %d', intval($_GET['id']))->delete()){
					alert('success', L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
				}else{
					$this->error(L('DELETE FAILED CONTACT THE ADMINISTRATOR'));
				}
			}
		}else{
			alert('error', L('SELECT_POSITION_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function user_role_relation(){
		if(!session('?name') || !session('?user_id')){
			redirect(U('User/login/'), 0, L('PLEASE_LOGIN_FIRSET'));
		}
		//用户添加到岗位
		if($_GET['by'] == 'user_role'){
			if($_GET['id']){
				$this->user = M('User')->where('user_id = %d', $_GET['id'])->find(); //占位符操作 %d整型 %f浮点型 %s字符串 
				
				$department = M('roleDepartment');
				$department_list = $department->select();	
				$departmentList = getSubDepartment(0, $department_list, '');				

				$role = M('Role');				
				foreach($departmentList as $key => $value) {					
					$roleList = $role->where('department_id =' . $value['department_id'])->select();
					$departmentList[$key]['roleList'] = $roleList;				
				}

				$this->assign('departmentList', $departmentList);
				$this->display('User:user_role');
			} elseif($_POST['user_id']){
				$m_user = M('user');
				$user = $m_user->where('user_id = %d' , $_POST['user_id'])->find();
				if($user['status'] == 0){
					alert('error', L('GRANT_PERMISSION_FAILED_FOR_NOT_PASS_AUDIT', array($user['name'])),$_SERVER['HTTP_REFERER']);
				} elseif($user['status'] == -1){
					alert('error', L('GRANT_PERMISSION_FAILED_FOR_NOT_PASS_AUDIT', array($user['name'])),$_SERVER['HTTP_REFERER']);
				} else {
					$role_ids = is_array($_POST['role']) ? implode(',', $_POST['role']) : '';
					$m_role = M('role');	
					$m_role->where("role_id in ('%s')", $role_ids)->setField('user_id', $_POST['user_id']);
					$m_role->where("role_id not in ('%s') and user_id=%d", $role_ids, $_POST['user_id'])->setField('user_id', '');
					
					alert('success', L('EDIT_SOMEONE_POSITION_SUCCESS', array($user['name'])),$_SERVER['HTTP_REFERER']);
				}
			}else{
				alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
			}
		//岗位添加用户
		}else if($_GET['by'] == 'role_user'){
			$role = M('role');
			if($_GET['role_id']){
				$this->role = $role->where('role_id = %d',$_GET['role_id'])->find();
				$this->userList =  M('user')->where('status = %d',1)->select();
				$this->display('User:role_user_add');
			}elseif($_POST['role_id']){
				$role->create();
				$m_user = M('user');
				$user = $m_user->where('user_id = %d' , $_POST['user_id'])->find();
				if (!$user['role_id']) {
					$m_user->where('user_id = %d' , $_POST['user_id'])->setField('role_id', $_POST['role_id']);
				}
				if($role->save()){
					alert('success',L('SETTING_SUCCESS'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error',L('SETTING_FAILED'),$_SERVER['HTTP_REFERER']);
				}			
			}
		}
	}
	
	public function changRole(){
		
	}
	
	public function getRoleList(){	
		$idArray = getSubRoleId();
		$roleList = array();
		foreach($idArray as $roleId){				
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		
		$this->ajaxReturn($roleList, '', 1);
	}
	public function weixinbinding(){
		if($_POST['submit']){
			if(!$weixinid = trim($_POST['id'])){
				alert('error', L('PARAMETER_ERROR'),U('User/notice')); 
			}
			if((!isset($_POST['name']) || $_POST['name'] =='')||(!isset($_POST['password']) || $_POST['password'] =='')){
				alert('error', L('INVALIDATE_USER_NAME_OR_PASSWORD'),U('User/weixinbinding').'&id='.$weixinid); 
			}elseif (isset($_POST['name']) && $_POST['name'] != ''){
				$m_user = M('user');
				$user = $m_user->where(array('name' => trim($_POST['name'])))->find();
				if ($user['password'] == md5(md5(trim($_POST['password'])) . $user['salt'])) {
					$m_user->where(array('user_id' => $user['user_id']))->save(array('weixinid'=>$weixinid));
					alert('error', L('BIND_SUCCESS'),U('User/notice'));
				} else {
					alert('error', L('INCORRECT_USER_NAME_OR_PASSWORD'),U('User/weixinbinding').'&id='.$weixinid); 				
				}
			}
		}else{
			if(!$weixinid = trim($_GET['id'])){
				alert('error', L('PARAMETER_ERROR'),U('user/notice')); 
			}else{
				$this->assign('id',$weixinid);
			}
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function notice(){
		$this->alert = parseAlert();
		$this->display();
	}
	public function weixin(){
		$weixin = M('Config')->where('name = "weixin"')->getField('value');
		$weixin_config = unserialize($weixin);
		$this->assign('weixin_config',$weixin_config);
		$this->display();
	}
	
	
}