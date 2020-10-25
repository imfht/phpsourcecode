<?php 
// 
class IndexAction extends Action {
    
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index','widget_edit','widget_delete','widget_add','calendar','sortcharts')
		);
		B('Authenticate', $action);
	}
	
	public function index(){
		$user = M('User');
		$m_announcement = M('announcement');
		$dashboard = $user->where('user_id = %d', session('user_id'))->getField('dashboard');
		$widget = unserialize($dashboard);		
		$this->widget = $widget;
		if (!F('smtp')) {
			alert('info', L('NOT_CONFIGURED_SMTP_INFORMATION_CLICK_HERE_TO_SET',array(U('setting/smtp'))));
		}
		if (!F('defaultinfo')) {
			alert('info', L('SYSTEM_INFORMATION_NOT_CONFIGURED_BY_DEFAULT_CLICK_HERE_TO_SET',array(U('setting/defaultinfo'))));
		}
		$where['department'] = array('like', '%('.session('department_id').')%');
		$where['status'] = array('eq', 1);
		$this->announcement_list = $m_announcement->where($where)->order('order_id')->select();
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function widget_edit(){
		$user = M('User');
		$dashboard = $user->where('user_id = %d', session('user_id'))->getField('dashboard');
		$widgets = unserialize($dashboard);
		if(isset($_GET['id']) && $_GET['id']!=''){
			/**
			 * 所有的小部件
			 * Function : 判断模块下的某个操作是否有权限
			 * @action  : 默认使用index操作来判断权限
			 */
			$widget_module = array(
				array('module'=>'customer','action'=>'index','tag'=>'Salesfunnel','name'=>'销售漏斗'),
				array('module'=>'customer','action'=>'index','tag'=>'Customerorigin','name'=>'客户来源'),
				array('module'=>'log','action'=>'index','tag'=>'Notepad','name'=>'便笺'),
				array('module'=>'finance','action'=>'index','tag'=>'Receivemonthly','name'=>'月度财务'),
				array('module'=>'finance','action'=>'index','tag'=>'Receiveyearcomparison','name'=>'财务年度对比')
			);
			//如果没有权限，从数组中去除
			foreach($widget_module as $k=>$v){
				if($v['module'] == 'log') continue;//默认便笺所有人都有权限
				if(!vali_permission($v['module'], $v['action'])){
					unset($widget_module[$k]);
				}
			}
			
			$this->widget_module = $widget_module;
			$this->edit_demo = $widgets[$_GET['id']];
			$this->display();
		} elseif(isset($_POST['widget_id']) && $_POST['widget_id']!='') {
			$title = $_POST['title']!='' && isset($_POST['title']) ? $_POST['title'] : '未定义组件';	
			$widgets[$_POST['widget_id']]['title'] = $title;
			$widgets[$_POST['widget_id']]['widget'] = $_POST['widget'];
			$newdashboard['dashboard'] = serialize($widgets);
			
			if($user->where('user_id = %d', session('user_id'))->save($newdashboard)){
				alert('success', L('MODIFY_THE_COMPONENT_INFORMATION_SUCCESSFULLY',array($_POST['widget'])), U('index/index'));
			}else{
				alert('error', L('MODIFY_THE_COMPONENT_INFORMATION_NO_CHANGE',array($_POST[widget])), U('index/index'));
			}
		}
	}
	
	public function widget_delete(){
		if(isset($_GET['id']) && $_GET['id']!=''){
			$user = M('User');
			$dashboard = $user->where('user_id = %d', session('user_id'))->getField('dashboard');
			$widget = unserialize($dashboard);
			unset($widget[$_GET['id']]);
			foreach($widget as $key=>$value){
				$widget[$key]['id'] = $key;
			}
			$newdashboard['dashboard'] = serialize($widget);
			if($user->where('user_id = %d', session('user_id'))->save($newdashboard)){
				alert('success', L('THE_COMPONENT_WAS_REMOVED_SUCCESSFULLY'), U('index/index'));
			}else{
				alert('error', L('THE_COMPONENT_WAS_REMOVED_FAILURE'),$_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	//serialize  unserialize
	public function widget_add(){
		if($this->isPost()){
			if($_POST['widget']){
				$user = M('User');
				$title = $_POST['title']!='' && isset($_POST['title']) ? $_POST['title'] : L('UNNAMED_COMPONENT');
				$dashboard = $user->where('user_id = %d', session('user_id'))->getField('dashboard');
				$widget = unserialize($dashboard);
				if(!is_array($widget)){
					$widget = array();
				}
				$widget[] = array('widget'=>$_POST['widget'], 'title'=>$title);
				foreach($widget as $key=>$value){
					$widget[$key]['id'] = $key;
				}
				$newdashboard['dashboard'] = serialize($widget);
				if($user->where('user_id = %d', session('user_id'))->save($newdashboard)){
					alert('success', L('ADD_COMPONENTS_TO_SUCCESS'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				alert('error', L('ADD_THE_COMPONENT_FAILS_PLEASE_FILL_IN_THE_COMPONENT_NAME'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			/**
			 * 所有的小部件
			 * Function : 判断模块下的某个操作是否有权限
			 * @action  : 默认使用index操作来判断权限
			 */
			$widget_module = array(
				array('module'=>'customer','action'=>'index','tag'=>'Salesfunnel','name'=>'销售漏斗'),
				array('module'=>'customer','action'=>'index','tag'=>'Customerorigin','name'=>'客户来源'),
				array('module'=>'log','action'=>'index','tag'=>'Notepad','name'=>'便笺'),
				array('module'=>'finance','action'=>'index','tag'=>'Receivemonthly','name'=>'月度财务'),
				array('module'=>'finance','action'=>'index','tag'=>'Receiveyearcomparison','name'=>'财务年度对比')
			);
			//如果没有权限，从数组中去除
			foreach($widget_module as $k=>$v){
				if($v['module'] == 'log') continue;//默认便笺所有人都有权限
				if(!vali_permission($v['module'], $v['action'])){
					unset($widget_module[$k]);
				}
			}
			$this->widget_module = $widget_module;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	/**
	 * @author 		: myron
	 * @function	: 首页日历获取任务和日程数据
	 * @return		: 任务和日程
	 **/
	public function calendar(){
		$role_id = session('role_id');
		$month_start = strtotime(date('Y-m-1',time()));	//本月开始时间
		$month_end = $month_start+(30*86400)-1;			//本月开始时间
		$date_begin = $month_start - 86400*6;			//本月1号6天前(日历上最多显示1号前六天)
		$date_end = $month_end + 86400*14;				//本月最后一天14天后(日历上最多显示月末14天后)

		//任务
		$taskData = array();
		$m_task = M('task');
		$where['owner_role_id']  = array('like', "%,$role_id,%");
		$where['about_roles']  = array('like',"%,$role_id,%");
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$map['create_date'] = array('egt', $date_begin);
		//$map['due_date'] = array('elt', $date_end);
		$map['is_deleted'] = array('eq', 0);
		$map['status'] = array('neq', '完成');
		$map['isclose'] = array('eq', 0);

		$task = $m_task->field('task_id, subject, create_date, due_date, "task" as type')->where($map)->order('create_date asc')->select();
		foreach($task as $k=>$v){
			$j = 0;
			for($i=$date_begin;$i<=$date_end;$i+=86400){
				$j=$i+86400;
				//每一天
				if($v['create_date'] < $j && $v['due_date'] >= $i){
					$url = U('task/index','field=subject&condition=is&act=search&search='.urlencode($v['subject']));
					$taskData[] = array(
						'title'=> '<a href="'.$url.'" target="_blank">'.$v['subject'].'</a>',
						'description'=>'',
						'datetime'=>$i,
						'type'=>'task'
					);
				}
			}
		}
		
		//日程
		$eventData = array(); 
		$m_event = M('event');
		$condition['owner_role_id']  = array('eq', $role_id);
		$condition['start_date'] = array('egt', $date_begin);
		// $condition['end_date'] = array('elt', $date_end);
		$condition['is_deleted'] = array('eq', 0);
		$condition['isclose'] = array('eq', 0);
		
		$event = $m_event->field('event_id,subject, start_date, end_date, "event" as type')->where($condition)->order('create_date desc')->select();
		foreach($event as $k=>$v){
			$j = 0;
			for($i=$date_begin;$i<=$date_end;$i+=86400){
				$j=$i+86400;
				//每一天
				if($v['start_date'] < $j && $v['end_date'] >= $i){
					$url = U('event/index','field=subject&condition=is&act=search&search='.urlencode($v['subject']));
					$eventData[] = array(
						'title'=>'<a href="'.$url.'" target="_blank">'.$v['subject'].'</a>',
						'description'=>'',
						'datetime'=>$i,
						'type'=>'event'
					);
				}
			}
		}

		$calendarData = array_merge($taskData, $eventData);
		$this->ajaxReturn($calendarData,'success',1);
	}
	
	//首页图表排序
	public function sortCharts(){
		$chart_arr = explode(',',$_POST['chart_arr']);	//用户拖动后的顺序
		$m_user = M('user');
		$dashboardSer = $m_user->where('role_id = %d', session('role_id'))->getField('dashboard');	//拖动前数据库的顺序
		$dashboard = unserialize($dashboardSer);
		$newdashboard = array();
		foreach($chart_arr as $val){
			foreach($dashboard as $vv){
				if($val == $vv['id']){
					$newdashboard[] = $vv;		//交换位置
				}
			}
		}
		$dashboardData = serialize($newdashboard);
		$m_user->where('role_id = %d', session('role_id'))->setField('dashboard',$dashboardData);
	}
}