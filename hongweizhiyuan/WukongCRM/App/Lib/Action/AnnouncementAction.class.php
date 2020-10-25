<?PHP 
class AnnouncementAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('getannouncement')
		);
		B('Authenticate', $action);
	}

	public function index(){
		$m_announcement = M('Announcement'); // 实例化User对象
		import('@.ORG.Page');// 导入分页类
		$where = array();
		$params = array();
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? 'title|content' : $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if	('create_time' == $field || 'update_time' == $field) $search = is_numeric($search)?$search:strtotime($search);
			switch ($condition) {
				case "is" : $where[$field] = array('eq',$search);break;
				case "isnot" :  $where[$field] = array('neq',$search);break;
				case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where[$field] = array('like',$search.'%');break;
				case "end_with" :  $where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $where[$field] = array('eq','');break;
				case "is_not_empty" :  $where[$field] = array('neq','');break;
				case "gt" :  $where[$field] = array('gt',$search);break;
				case "egt" :  $where[$field] = array('egt',$search);break;
				case "lt" :  $where[$field] = array('lt',$search);break;
				case "elt" :  $where[$field] = array('elt',$search);break;
				case "eq" : $where[$field] = array('eq',$search);break;
				case "neq" : $where[$field] = array('neq',$search);break;
				case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where[$field] = array('gt',$search+86400);break;
				default : $where[$field] = array('eq',$search);
			}
			$params = array('field='.$field, 'condition='.$condition, 'search='.trim($_REQUEST["search"]));
		}
		$p = isset($_GET['p'])?$_GET['p']:1;
		$count = $m_announcement->count();// 查询满足要求的总记录数
		$list = $m_announcement->where($where)->order('status,order_id')->Page($p.',10')->select();
		$Page = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$Page->parameter = implode('&', $params);
		$userRole = M('userRole');
		foreach($list as $k => $v){
			$list[$k]['owner'] = D('RoleView')->where('role.role_id = %d', $v['role_id'])->find();
		}
		
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$Page->show());// 赋值分页输出
		$this->alert=parseAlert();
		$this->display(); // 输出模板
	}
	public function announcementOrder(){
		if ($this->isGet()) {
			$m_announcement = M('Announcement');
			$a = 0;
			foreach (explode(',', $_GET['postion']) as $v) {
				$a++;
				$m_announcement->where('announcement_id = %d', $v)->setField('order_id',$a);
			}
			$this->ajaxReturn('1', L('SUCCESSFULLY EDIT'), 1);
		} else {
			$this->ajaxReturn('0', L('EDIT FAILED'), 1);
		}
	}
	public function add(){
		if($_POST['submit']){
			$title = trim($_POST['title']);
			if ($title == '' || $title == null) {
				alert('error',L('TITLE CAN NOT NULL'),$_SERVER['HTTP_REFERER']);
			}
			$d_announcement = M('Announcement');
			if($d_announcement->create()){
				$d_announcement->role_id = session('role_id');
				$d_announcement->department = '('.implode('),(', $_POST['announce_department']).')';
				$d_announcement->create_time = time();
				$d_announcement->update_time = time();
				$d_announcement->add();
				if($_POST['submit'] == L('SAVE')) {
					alert('success', L('NOTICE TO ADD SUCCESS'), U('announcement/index'));
				} else {
					alert('success', L('ADD A SUCCESS'), U('announcement/add'));
				}
			}else{
				$this->error($d_announcement->getError());
			}

		}else{
			$m_department = M('RoleDepartment');
			$department_list = $m_department->select();	
			$this->assign('department_list', getSubDepartment(0,$department_list,'', 1));
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function view(){
		if($_GET['id']){
			$m_announcement = M('Announcement');
			$m_announcement->where('announcement_id=%d',$_GET['id'])->setInc('hits');
			$announcement = $m_announcement->where('announcement_id = %d ', $_GET['id'])->find();
			$announcement['owner'] = D('RoleView')->where('role.role_id = %d', $announcement['role_id'])->find();
			$m_userRole = M('userRole');
			$announcement['username']  = $m_userRole->where('role_id = %d',$announcement['role_id'])->getField('name');
			
			$m_department = M('RoleDepartment');
			$alldepartment_list = $m_department->select();	
			$department_list = getSubDepartment(0,$alldepartment_list,'', 1);
			$department_id_array = explode(',', $announcement['department']);
			foreach($department_list as $k=>$v){
				if(in_array('('.$v['department_id'].')', $department_id_array)) $department_list[$k]['checked'] = 'checked';
			}
			
			$pre = $m_announcement->where('create_time < %d', $announcement['create_time'])->order('create_time desc')->limit(1)->find();
			if($pre) $this->pre_href = U('announcement/view', 'id='.$pre['announcement_id']);
			$next = $m_announcement->where('create_time > %d', $announcement['create_time'])->limit(1)->find();
			if($next) $this->next_href = U('announcement/view', 'id='.$next['announcement_id']);
			
			$this->department_list = $department_list;
			$this->announcement = $announcement;
			$this->alert = parseAlert();
			$this->display();
		}else{
			$this->error(L('PARAMETER ERROR'));
		}
	}
	
	public function changeStatus(){
		$m_announcement = M('Announcement');
		$announcement_id = intval($_GET['id']);
		
		if ($announcement_id) {
			$announcement = $m_announcement->where('announcement_id = %d', $announcement_id)->find();
			if(!session('?admin') && $announcement['role_id'] != session('role_id')){
				alert('error','HAVE NOT PRIVILEGES', $_SERVER['HTTP_REFERER']);
			}
			if ($announcement['status'] == 1) {
				$m_announcement->where('announcement_id = %d', $announcement_id)->setField('status', 2);
				alert('success',L('MODIFY SUCCESS HAS BEEN DISCONTINUED'),$_SERVER['HTTP_REFERER']);
			} elseif($announcement['status'] == 2) {
				$m_announcement->where('announcement_id = %d', $announcement_id)->setField('status', 1);
				alert('success',L('MODIFY SUCCESS HAS BEEN PUBLISHED'),$_SERVER['HTTP_REFERER']);
			} else {
				alert('success',L('SYSTEM ERROR PLEASE CONTACT YOUR ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error',L('PARAMETER ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function edit(){
		$announcement_id = $_POST['announcement_id']?intval($_POST['announcement_id']):intval($_GET['id']);
		if($announcement_id && !check_permission($announcement_id, 'announcement', 'role_id')) $this->error(L('HAVE NOT PRIVILEGES'));
		if($this->isPost()){
			$title = trim($_POST['title']);
			if ($title == '' || $title == null) {
				alert('error',L('THE NAME CANNOT BE EMPTY'),$_SERVER['HTTP_REFERER']);
			}
			$m_announcement = M('Announcement');
			if($m_announcement->create()){
				$m_announcement->department = '('.implode('),(', $_POST['announce_department']).')';
				$m_announcement->update_time = time();
				if($m_announcement->save()){
					if($_POST['submit'] == L('SAVE')) {
						alert('success', L('ANNOUNCEMENT SAVED SUCCESSFULLY'), U('announcement/index'));
					} else {
						alert('success', L('SAVE THE SUCCESS PLEASE CONTINUE TO INPUT'), U('announcement/add'));
					}
				} else {
					alert('error', L(''),U('announcement/index'));
				}
			}else{
				$this->error($m_announcement->getError());
			}
		}elseif($_GET['id']){
			$m_announcement = M('Announcement');
			$m_department = M('RoleDepartment');
			$department_list = getSubDepartment(0,$m_department->order('department_id')->select(),'', 1);
			$announcement = $m_announcement->where('announcement_id = %d',$_GET['id'])->find();
			$department_id_array = explode(',', $announcement['department']);

			foreach($department_list as $k=>$v){
				if(in_array('('.$v['department_id'].')', $department_id_array)) $department_list[$k]['checked'] = 'checked';
			}
			$this->assign('department_list', $department_list);
			$this ->announcement = $announcement;
			$this->display();
		}else{
			$this->error(L('PARAMETER ERROR'));
		}
	}
	public function delete(){
		$m_announcement = M('Announcement');
		$announcement_idarray = $_POST['announcement_id'];
		if (is_array($announcement_idarray)) {
			if (!session('?admin')) {
				foreach ($announcement_idarray as $v) {
					if (!$m_announcement->where('announcement_id = %d and role_id = %d', $v, session('role_id'))->find()){
						alert('error', L('YOU DONOT HAVE ALL THE PERMISSIONS ONLY THE AUTHOR OR THE ADMINISTRATOR CAN DELETE'),$_SERVER['HTTP_REFERER']);
					}
				}
			}
			if ($m_announcement->where('`announcement_id` in (%s)', join(',', $announcement_idarray))->delete()) {
				alert('success', L('DELETED SUCCESSFULLY'),U('Announcement/index'));
			} else {
				$this->error(L('DELETE FAILED CONTACT THE ADMINISTRATOR'));
			}
		} elseif($_GET['id']) {
			if (!session('?admin')) {
				if (!$m_announcement->where('announcement_id = %d and role_id = %d', $_GET['id'], session('role_id'))->find()){
					alert('error', L('YOU DONOT HAVE ALL THE PERMISSIONS ONLY THE AUTHOR OR THE ADMINISTRATOR CAN DELETE'),$_SERVER['HTTP_REFERER']);
				}
			}
			
			if($m_announcement->where('announcement_id = %d', $_GET['id'])->delete()){
				alert('success', L('DELETED SUCCESSFULLY'),U('Announcement/index'));
			}else{
				$this->error(L('DELETE FAILED CONTACT THE ADMINISTRATOR'));
			}
		} else {
			alert('error', L('PLEASE CHOOSE TO DELETE ANNOUNCEMENT'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	 * 首页获取公告
	 **/
	public function getAnnouncement(){
		$m_announcement = M('announcement');
		$where['department'] = array('like', '%('.session('department_id').')%');
		$where['status'] = array('eq', 1);
		$announcement = $m_announcement->where($where)->order('order_id')->limit(7)->select();
		if(!empty($announcement)){
			$this->ajaxReturn($announcement,'success',1);
		}else{
			$this->ajaxReturn('','---暂无数据---',0);
		}
	}
}
