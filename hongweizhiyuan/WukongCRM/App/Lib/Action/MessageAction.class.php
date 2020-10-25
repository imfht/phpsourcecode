<?php 
class MessageAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('view', 'send','delete', 'ajaxsend', 'index','tips','setread')
		);
		B('Authenticate', $action);
	}
	
	public function index(){
		import("@.ORG.Page");
		$m_r_message = D('MessageReceiveView');
		$m_s_message = D('MessageSendView');
		$r_where ='';
		$s_where='';
		$field = empty($_REQUEST['field']) ? '' : trim($_REQUEST['field']);
		$condition = empty($_REQUEST['condition']) ? '' : trim($_REQUEST['condition']);
		$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
		if	($field == 'send_time' || $field == 'read_time') $search = strtotime($search);
		if($field == 'from_role_id' || $field == 'to_role_id'){
			if(strpos(L('SYSTEM_ADMINISTRATOR'),$search) !== false){
				$r_where[$field] = array('eq',0);
				$s_where[$field] = array('eq',0);
			}else{
				$username = trim($search);
				$id_array = M('User')->where('name like "%s"','%'.$username.'%')->getField('role_id',true);
				$ids = implode(',',$id_array);
				$r_where[$field] = array('in',$ids);
				$s_where[$field] = array('in',$ids);
			}
		}
		if (!empty($field) && $field != 'from_role_id' && $field != 'to_role_id') {
			switch ($condition) {
				case "is" : $r_where[$field] = array('eq',$search);break;
				case "isnot" :  $r_where[$field] = array('neq',$search);break;
				case "contains" :  $r_where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $r_where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $r_where[$field] = array('like',$search.'%');break;
				case "end_with" :  $r_where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $r_where[$field] = array('eq','');break;
				case "is_not_empty" :  $r_where[$field] = array('neq','');break;
				case "gt" :  $r_where[$field] = array('gt',$search);break;
				case "egt" :  $r_where[$field] = array('egt',$search);break;
				case "lt" :  $r_where[$field] = array('lt',$search);break;
				case "elt" :  $r_where[$field] = array('elt',$search);break;
				case "eq" : $r_where[$field] = array('eq',$search);break;
				case "neq" : $r_where[$field] = array('neq',$search);break;
				case "between" : $r_where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $r_where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $r_where[$field] = array('gt',$search+86400);break;
				default : $r_where[$field] = array('eq',$search);
			}
			$s_where = $r_where;
		}
		if ($_GET['type'] == 'send') {
			$p2 = isset($_GET['s_p']) ? intval($_GET['s_p']) : 1 ;
			$p1 = 1;
		} elseif ($_GET['type'] == 'receive'){
			$p1 = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
			$p2 = 1;
		} else {
			$p1 = 1;
			$p2 = 1;
		}
		$r_where['to_role_id'] = session('role_id');
		$r_where['message.status'] = array('neq', 1);
		
		$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
		$str_params = implode('&',$params);
		$receive_list = $m_r_message->where($r_where)->order('send_time desc')->page($p1.',10')->select();
		foreach($receive_list as $k=>$v){
			$pre_content = strip_tags($v['content']);
			$pre_content_len = mb_strlen($pre_content,'utf-8');
			if($pre_content_len <= 35){
				$receive_list[$k]['pre_content'] = $pre_content;
			}else{
				$pre_content = mb_substr($pre_content,0,35,'utf-8');
				$receive_list[$k]['pre_content'] = $pre_content.' . . .';
			}
		}

		$count1 = $m_r_message->where($r_where)->count();
		$r_where['read_time'] = 0;
		$new_num = $m_r_message->where($r_where)->count();
		
		$receive_page = new Page($count1,10);
		$receive_page->parameter   .=   "type=receive" . '&'.$str_params;
		$this->assign('receive_page', $receive_page->show());
		
		$s_where['from_role_id'] = session('role_id');
		$s_where['message.status'] = array('neq', 2);
		
		$send_list = $m_s_message->where($s_where)->order('send_time desc')->page($p2.',10')->select();
		foreach($send_list as $k=>$v){
			$pre_content = strip_tags($v['content']);
			$pre_content_len = mb_strlen($pre_content,'utf-8');
			if($pre_content_len <= 35){
				$send_list[$k]['pre_content'] = $pre_content;
			}else{
				$pre_content = mb_substr($pre_content,0,35,'utf-8');
				$send_list[$k]['pre_content'] = $pre_content.' . . .';
			}
		}
		
		$count2 = $m_s_message->where($s_where)->count();
		$send_page = new Page($count2,10);
		$send_page->varPage = 's_p';
		$send_page->nowPage = $p2;
		$send_page->parameter   .=   "type=send" . '&'.$str_params;
		$this->assign('send_page', $send_page->show());	
		$type = empty($_POST['type']) ? '' : $_POST['type'];
		$this->assign('type',$type);
		$this->assign('receive_list',$receive_list);
		$this->assign('receive_list_num',$count1);
		$this->assign('new_num',$new_num);
		$this->assign('send_list',$send_list);
		$this->assign('send_list_num',$count2);
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function view(){
		$m_message = D('MessageReceiveView');
		$id = intval($_GET['id']);
		$where['message_id'] = $id;
		$where['_complex'] = array('to_role_id'=>session('role_id'),'from_role_id'=>session('role_id'),'_logic'=>'or');
		$info = $m_message->where($where)->find();
		if($info['read_time'] == 0 && $info['to_role_id'] == session('role_id')){
			$m_message->where(array('message_id'=>$id,'to_role_id'=>session('role_id')))->save(array('read_time'=>time()));
		}
		$this->assign('info',$info);
		$this->display();
	}
	
	public function send(){
		if($_POST['submit']){
			$to_role = $_POST['to_role_id'];
			if(is_array($to_role)){
				foreach($to_role as $k=>$v){
					sendMessage($v, $_POST['content']);
				}
				alert('success',L('SEND_SUCCESS'),U('Message/index'));
			}else{
				if(sendMessage($_POST['to_role_id'],$_POST['content'])){
					alert('success',L('SEND_SUCCESS'), U('Message/index'));
				}else{
					alert('error',L('SEND_FAILY'),$_SERVER['HTTP_REFERER']);
				}
			}
			
		}elseif(intval($_GET['from_role_id'])){
			$user_info = M('User')->where(array('role_id'=>intval($_GET['from_role_id'])))->find();
			$this->assign('user_info',$user_info);
		}
		$d_role = D('RoleView');
		
		$departments_list = M('roleDepartment')->select();	
		foreach($departments_list as $k=>$v){
			$roleList = $d_role->where('position.department_id = %d', $v['department_id'])->select();
			$departments_list[$k]['user'] = $roleList;
		}
		$this->departments_list = $departments_list;
		$this->display();
	}
	
	public function ajaxSend(){
		if ($this->isAjax()){
			if(sendMessage($_POST['to_role_id'],$_POST['content'])){
				$this->ajaxReturn("",L('SEND_SUCCESSS'),1);
			}else{
				$this->ajaxReturn("",L('SEND_FAILYS'),0);
			}
		}else{
			alert('error', L('ILLEGAL_ACCESS'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function delete(){
		$m_message = M('message');
		if($this->isPost()){
			$message_id = is_array($_POST['message_id']) ? $_POST['message_id'] : '';
			if ('' == $message_id) { 
				alert('error', L('NOT_CHOOSE_ANY_CONTENT'), U('Message/index'));
			} else {
				if($_GET['model'] == 'receive'){
					foreach($message_id as $k => $v){
						$message = $m_message->where('message_id = %d and to_role_id= %d', $v, session('role_id')) -> find();
						if($message['status'] == 2 || $message['from_role_id'] == 0){
							$m_message->where('message_id = %d', $v)->delete();
						}else{
							$m_message->where('message_id = %d', $v)->setField('status', 1);
						}
					}
					alert('success',L('DELETE_SUCCESS'),U('Message/index'));
				}elseif($_GET['model'] == 'send'){
					foreach($message_id as $k => $v){
						$message = $m_message->where('message_id = %d and from_role_id= %d', $v, session('role_id')) -> find();
						if($message['status'] == 1 || $message['from_role_id'] == 0){
							$m_message->where('message_id = %d', $v)->delete();
						}else{
							$m_message->where('message_id = %d', $v)->setField('status', 2);
						}
					}
					alert('success',L('DELETE_SUCCESS'),U('Message/index'));
				}else{
					alert('error',L('PARAMETER_ERROR_DELETE_FALILY'),$_SERVER['HTTP_REFERER']);
				}
			}
		}else{
			$id = intval($_GET['id']);
			$message = $m_message->where('message_id = %d', $id) -> find();
			if($id){
				if($message['from_role_id'] == 0){
					if($m_message->where('message_id = %d', $id)->delete()){
						alert('success',L('DELETE_SUCCESS'),U('Message/index'));
					}else{
						alert('error',L('DELETE_FAILY'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					if($message['status'] == 0){
						$status = $message['to_role_id'] == session('role_id') ?  1 : 2 ;
						if($m_message->where('message_id = %d', $id)->setField('status', 1)){
							alert('success',L('DELETE_SUCCESS'), U('Message/index'));
						}else{
							alert('error',L('DELETE_FAILY'), $_SERVER['HTTP_REFERER']);
						}
					}elseif($message['status'] == 1 || $message['status'] == 2){
						if($m_message->where('message_id = %d', $id)->delete()){
							alert('success',L('DELETE_SUCCESS'), U('Message/index'));
						}else{
							alert('error',L('DELETE_FAILY'), $_SERVER['HTTP_REFERER']);
						}
					}
				}
			}else{
				alert('error',L('PARAMETER_ERROR_CONTCART_ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	/**
	 * xxx_count格式的数据为卡片提醒数据，否则为导航提醒数据
	 */
	public function tips(){
		//导航顶部数字提示
		$m_message = M('message');
		$new_num[message] = $m_message->where(array('to_role_id' => session('role_id'),'read_time' => 0,'status'=>array('neq', 1)))->count();		
		$m_task = M('Task');
		$new_num[task] = $m_task->where('owner_role_id like "%s" and isclose = 0 and status <> "'.L('COMPLETE').'" and is_deleted <> 1 and due_date > %d', '%'.session('role_id').'%',time())->count();
		
		$m_event = M('Event');
		$current_time = strtotime(date('Y-m-d', time()));
		$new_num[event] = $m_event->where("owner_role_id = %d and isclose = 0 and is_deleted <> 1 and $current_time >= start_date and $current_time <= end_date", session('role_id'))->count();
		
		$m_contract = M('contract');
		$days = C('defaultinfo.contract_alert_time')?intval(C('defaultinfo.contract_alert_time')):30;
		$temp_time = $current_time+$days*86400;
		$new_num[contract] = $m_contract->where("owner_role_id = %d and is_deleted <> 1 and $temp_time >= end_date and end_date >= $current_time", session('role_id'))->count();
	
		//导航下面卡片提示
		//从用户表中读取“最后阅读时间”，如果创建时间大于最后阅读时间，则提醒，否则不提醒
		$m_user = M('user');
		$last_read_time_js = $m_user->where('role_id = %d', session('role_id'))->getField('last_read_time');
		if(!empty($last_read_time_js)){
			$last_read_time = json_decode($last_read_time_js, true);
		}
		//任务
		$new_num[task_count] = $m_task->where('owner_role_id like "%s" and isclose = 0 and status <> "'.L('COMPLETE').'" and is_deleted <> 1 and due_date > %d and create_date > %d', '%'.session('role_id').'%',time(), $last_read_time['task'])->count();
		//日程
		$new_num[event_count] = $m_event->where("owner_role_id = %d and isclose = 0 and is_deleted <> 1 and $current_time >= start_date and $current_time <= end_date and create_date > %d", session('role_id'), $last_read_time['event'])->count();
		//合同
		$new_num[contract_count] = $m_contract->where("owner_role_id = %d and is_deleted <> 1 and $temp_time >= end_date and end_date >= $current_time and create_time > %d", session('role_id'), $last_read_time['contract'])->count();

		$this->ajaxReturn($new_num,"",1);
	}
	
	//站内信标记为已读
	public function setRead(){
		$message_idAll=$_POST['message_id'];
		if($message_idAll == ''){
			alert('error',L('PLEASE_CHOOSE_MESSAGE_TO_SET_READ'),$_SERVER['HTTP_REFERER']);
		}
		else{
			$m_message = M('message');
			$where['message_id'] =array('in' ,$message_idAll);
			$where['read_time'] =0;
			$Result =$m_message ->where($where) ->setField('read_time',time());
			if($Result)
			{
				alert('success',L('SUCCESS_TO_SET_MESSAGE_READ'),$_SERVER['HTTP_REFERER']);
			}else{
			    alert('error',L('FALSE_TO_SET_MESSAGE_READ'),$_SERVER['HTTP_REFERER']);
			}
		}
	}
}