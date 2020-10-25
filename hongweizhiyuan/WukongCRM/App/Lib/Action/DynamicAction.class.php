<?PHP 
/**
 * 动态模块	
 * myron
 **/
class DynamicAction extends Action{
	
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index','adddynamiccomment','deletecomment','more')
		);
		B('Authenticate', $action);
	}
	
	/**
	 * 动态首页
	 *
	 **/
	public function index(){
		$m_action_log = M('actionLog');
		$m_comment = M('comment');
		$where = array();	//查询条件
		$where['action_delete'] = 0;
		$opeartion = 'view';	//默认都跳转到view界面
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$reply = isset($_GET['reply']) ? trim($_GET['reply']) : '';
		//module=log时，module_id为log表的log_id; module为dynamic时，module_id为action_log表的log_id
		switch ($by) {
			case 'log' : 
				$where['module_name'] =  array('eq', 'log');
				break;
			case 'crm' : 
				$where['module_name'] =  array('neq','log');
				break;
		}
		switch ($reply) {
			case 'tome' : 
				//回复我的
				if($by == 'log'){
					//如果是日志，查action_id
					$module_idArr = $m_comment->group('module_id')->where('to_role_id = %d and module = "%s"',session('role_id'),'log')->order('create_time desc')->getField('module_id',true);
					$where['action_id'] = array('in', $module_idArr);
				}elseif($by == ''){
					//如果为空，先查log，根据comment表的molule_id查到action_log表的action_id对应的log_id，再查动态，根据comment表的module_id，查到action_log表的log_id
					$log_idArr = $m_comment->group('module_id')->where('to_role_id = %d and module = "%s"',session('role_id'),'log')->order('create_time desc')->getField('module_id',true);//comment 表的 module_id
					$module_idArrA = $m_action_log->group('action_id')->where(array('action_id'=>array('in',$log_idArr),'module_name'=>'log','action_delete'=>0))->getField('log_id',true);//module=log时，根据module_id对应action_log表的action_id查到的action_log表的log_id
					$module_idArrB = $m_comment->where('to_role_id = %d and module <> "%s"',session('role_id'),'log')->group('module_id')->order('create_time desc')->getField('module_id',true);//module!=log时，根据module_id对应action_log表的log_id获取到action_log表的log_id
					empty($module_idArrA) ? $module_idArrA = array() : $module_idArrA = $module_idArrA;
					empty($module_idArrB) ? $module_idArrB = array() : $module_idArrB = $module_idArrB;
					$log_id = array_merge($module_idArrA, $module_idArrB);
					$where['log_id'] = array('in',$log_id);
				}else{
					$module_idArr = $m_comment->group('module_id')->where('to_role_id = %d and module <> "%s"',session('role_id'),'log')->order('create_time desc')->getField('module_id',true);
					$where['log_id'] = array('in', $module_idArr);
				}
				break;
			case 'toother' : 
				//我回复的
				if($by == 'log'){
					//如果是日志，查action_id
					$module_idArr = $m_comment->group('module_id')->where('creator_role_id = %d and module = "%s"',session('role_id'),'log')->order('create_time desc')->getField('module_id',true);
					$where['action_id'] = array('in', $module_idArr);
				}elseif($by == ''){
					//如果为空，先查log，根据comment表的molule_id查到action_log表的action_id对应的log_id，再查动态，根据comment表的module_id，查到action_log表的log_id
					$log_idArr = $m_comment->group('module_id')->where('creator_role_id = %d and module = "%s"',session('role_id'),'log')->order('create_time desc')->getField('module_id',true);//comment 表的 module_id
					$module_idArrA = $m_action_log->group('action_id')->where(array('action_id'=>array('in',$log_idArr),'module_name'=>'log','action_delete'=>0))->getField('log_id',true);//module=log时，根据module_id对应action_log表的action_id查到的action_log表的log_id
					$module_idArrB = $m_comment->where('creator_role_id = %d and module <> "%s"',session('role_id'),'log')->group('module_id')->order('create_time desc')->getField('module_id',true);//module!=log时，根据module_id对应action_log表的log_id查到的action_log表的log_id
					empty($module_idArrA) ? $module_idArrA = array() : $module_idArrA = $module_idArrA;
					empty($module_idArrB) ? $module_idArrB = array() : $module_idArrB = $module_idArrB;
					$log_id = array_merge($module_idArrA, $module_idArrB);
					$where['log_id'] = array('in',$log_id);
				}else{
					$module_idArr = $m_comment->group('module_id')->where('creator_role_id = %d and module <> "%s"',session('role_id'),'log')->order('create_time desc')->getField('module_id',true);
					$where['log_id'] = array('in', $module_idArr);
				}
				break;
		}
		if(!empty($reply)){
			$params[] = "reply=" . trim($_GET['reply']);
		}
		
		if($_GET['department_id']){
			$department_id = intval($_GET['department_id']);
			//选中部门下的所有员工
			$subPositionIdArr = M('position')->where('department_id = %d',$department_id)->order('position_id asc')->getField('position_id',true);
			$subRoleIdArr = M('role')->where(array('position_id'=>array('in', $subPositionIdArr)))->getField('role_id',true);	//部门下role_id
			if(!session('admin')){
				//条件为选中部门下,我的下属员工的role_id
				$mySubRoleIdArr = getSubRoleId();	//我的下属role_id
				$where['role_id'] = array('in', array_intersect($subRoleIdArr,$mySubRoleIdArr));
			}else{
				$where['role_id'] = array('in', $subRoleIdArr);
			}
		}else{
			//条件为选中部门下,我的下属员工的role_id
			if(!session('?admin')){
				$where['role_id'] = array('in', getSubRoleId());
			}
		}

		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']);
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			if($field == 'role'){
				if(!empty($search)){
					$same_role_id_array = M('user')->where('name like "%s"', "%$search%")->getField('role_id',true);
					$role_id_array = getSubRoleId(true, 1);		//下属role_id
					$role_idArr = array_intersect($same_role_id_array,$role_id_array);	//交集
					$where['role_id'] = array('in',$role_idArr);
				}
			}elseif($field == 'content'){
				$where['content'] = array('like',"%$search%");
			}
			$params = array('field='.trim($_REQUEST['field']), 'search='.$search);
		}
		$action_log = $m_action_log->where($where)->page($p.',5')->order('create_time desc')->select();
		$count = $m_action_log->where($where)->count();
		import("@.ORG.Page");
		$Page = new Page($count,5);
		if (!empty($_GET['by'])) {
			$params[] = "by=" . trim($_GET['by']);
		}
		
		foreach($action_log as $k=>$v){
			if($v['module_name'] == 'finance'){
				$module_name = substr($v['param_name'],2);
			}else{
				$module_name = $v['module_name'];
			}
			$m_module_name = M($module_name);
			$pk_id = $m_module_name->getPk();
			$object_module = $m_module_name->where("$pk_id = %d", $v['action_id'])->find();
			$name = $object_module['name'];
			if(empty($name)){
				$name = $object_module['subject'];
			}
			
			
			//如果是日志，则追加日志内容
			if($v['action_name'] == 'mylog_add'){
				$apContent = $object_module['content'];
				$appHtml = '<p>'.$apContent.'</p>';
				//如果是日志，则跳转到mylog_view
				$action_name = 'mylog_view';
			}else{
				$appHtml = '';
				$action_name = strtolower($v['action_name']);
			}

			$action_log[$k]['creator'] = getUserByRoleId($v['role_id']);
			$username = $action_log[$k]['creator']['user_name'];
			$operation = L(strtolower($v['action_name'])).L('THE_IS');
			$module = L(strtolower($v['module_name']));
			$action_log[$k]['dynamic'] = $username.'&nbsp;'.$operation.$module.' - <a href="./index.php?m='.$v[module_name].'&a='.$action_name.'&'.$param_name.'&id='.$v[action_id].'">'.$name.'</a>'.$appHtml;
			
			//查评论
			//如果是评论日志，则从操作日志表中根据action_id查找，否则根据操作日志表的log_id查找
			if($v['module_name'] == 'log'){
				$comment = $m_comment->where('module = "%s" and module_id = %d', 'log', $v['action_id'])->order('create_time desc')->limit(0,5)->select();
				$comment_count = $m_comment->where('module = "%s" and module_id = %d', 'log', $v['action_id'])->order('create_time desc')->count();
			}else{
				$comment = $m_comment->where('module = "%s" and module_id = %d', 'dynamic', $v['log_id'])->order('create_time desc')->limit(0,5)->select();
				$comment_count = $m_comment->where('module = "%s" and module_id = %d', 'dynamic', $v['log_id'])->order('create_time desc')->count();
			}
			//$comment = $m_comment->where('(module = "%s" or module = "%s") and module_id = %d','log', 'dynamic', $v['log_id'])->order('create_time desc')->select();
			foreach($comment as $key=>$val){
				$comment[$key]['comment_role'] = getUserByRoleId($val['creator_role_id']);
			}
			$action_log[$k]['comment'] = $comment;
			$action_log[$k]['comment_count'] = $comment_count;
			$action_log[$k]['comment_left_count'] = intval($comment_count) - 5;
		}
		
		$this->parameter = implode('&', $params);
		$this->actionLog = $action_log;
		$this->userInfo = getUserByRoleId(session('role_id'));
		$this->department = getSubDepartment2(0,M('roleDepartment')->select());
		$show = $Page->show();		
		$this->page = $show;
		$this->alert = parseAlert();
		$this->display();
	}
	
	//写入评论
	public function addDynamicComment(){
		//如果module是log，则本条评论为日志评论（与日志模块下的评论共通），否则为动态评论
		if($_POST['module_name'] == 'log'){
			$data['module'] = 'log';
		}else{
			$data['module'] = 'dynamic';
		}
		$data['module_id'] = $_POST['module_id'];
		$data['content'] = $_POST['content'];
		$data['creator_role_id'] = session('role_id');
		$data['to_role_id'] = $_POST['to_role_id'];
		$data['create_time'] = time();
		
		//判断
		if(!session('?role_id')){
			$this->ajaxReturn('','请先登录',0);
		}
		if(empty($data['module_id']) || empty($data['content']) ||  empty($data['to_role_id'])){
			$this->ajaxReturn('','请输入评论内容！',0);
		}

		//两次评论间隔不能小于10秒
		$m_comment = M('comment');
		$last_comment = $m_comment->where('module = "%s" and module_id = %d and creator_role_id = %d and create_time > %d',$data['module'],$data['module_id'],session('role_id'),time()-10)->count();
		if($last_comment > 0){
			$this->ajaxReturn('','两次评论间隔不能小于10秒',0);
		}
		
		//执行数据库操作
		$comment_id = $m_comment->add($data);
		if($comment_id !== false){
			$user = M('user')->field('role_id,name,img')->where('role_id = %d', session('role_id'))->find();
			$comment['comment_id'] = $comment_id;
			$comment['role_id'] = $user['role_id'];
			$comment['user_name'] = $user['name'];
			$comment['img'] = $user['img'];
			$comment['time'] = date('Y-m-d H:i:s',time());
			//评论日志时，发送站内信通知
			if($_POST['messageAlert'] == 'message' && $_POST['module_name'] == 'log' && session('role_id') != $_POST['to_role_id']){
				sendMessage($_POST['to_role_id'], L('THE MAIN CONTENTS ARE AS FOLLOWS',array(createCommentAlertInfo('log', $_POST['module_id']),chr(10),$_POST['content'])),1);
			}
			$this->ajaxReturn($comment,'评论成功',1);
		}else{

			$this->ajaxReturn('','评论失败，请重试',0);
		}
	}
	
	//删除评论
	public function deleteComment(){
		$data['comment_id'] = intval($_POST['comment_id']);
		$data['creator_role_id'] = session('role_id');
		
		//判断
		if(!session('?role_id')){
			$this->ajaxReturn('','请先登录',0);
		}
		//是否存在
		$m_comment = M('comment');
		$comment = $m_comment->where('comment_id = %d and creator_role_id = %d',$data['comment_id'],$data['creator_role_id'])->find();
		if(empty($comment)){
			$this->ajaxReturn('','评论不存在或已被删除',0);
		}
		
		//执行数据库操作
		$result = $m_comment->where($data)->delete();
		if($result !== false){
			$this->ajaxReturn('','删除成功',1);
		}else{
			$this->ajaxReturn('','删除失败',0);
		}
	}
	
	//展开更多评论
	public function more(){
		$log_id = $_POST['log_id'];
		if(empty($log_id)){
			$this->ajaxReturn('error','参数错误',0);
		}
		$m_action_log = M('actionLog');
		$m_comment = M('comment');
		$actionLog = $m_action_log->where('log_id = %d', $log_id)->find();
		if(empty($actionLog)){
			$this->ajaxReturn('error','信息不存在或已删除！',0);
		}
		//查评论
		//如果是评论日志，则从操作日志表中根据action_id查找，否则根据操作日志表的log_id查找
		if($actionLog['module_name'] == 'log'){
			$comment = $m_comment->where('module = "%s" and module_id = %d', 'log', $actionLog['action_id'])->order('create_time desc')->limit(5,1000)->select();
		}else{
			$comment = $m_comment->where('module = "%s" and module_id = %d', 'dynamic', $actionLog['log_id'])->order('create_time desc')->limit(5,1000)->select();
		}
		foreach($comment as $key=>$val){
			$comment[$key]['comment_role'] = getUserByRoleId($val['creator_role_id']);
			$comment[$key]['time'] = date('Y-m-d H:i:s',$val['create_time']);
		}
		//println($comment);
		$this->ajaxReturn($comment,'success',1);
	}
	
	
}
