<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

class UserAction extends Action {
	/**
		* 用户列表
		*@param $json    为NULL输出模板。为1时输出列表数据到前端，格式为Json
		*@param $method  为1时，单独输出记录数
		*@examlpe 
	*/
    public function index($json=NULL,$method=NULL){
		$Public = A('Index','Public');		//加载IndexPublic类
		$Public->check('User',array('r'));	//用户检查
		
		//main
		if(!is_int((int)$json)){
			$json = NULL;
		}
		$view = C('DATAGRID_VIEW');		//获取试图状态
		$page_row = C('PAGE_ROW');		//获取默认显示条数
		if($json==1){
			$get_sort = $this->_get('sort');
			$get_order = $this->_get('order');
			$sort = isset($get_sort) ? strval($get_sort) :  'username'; //，默认排序字段
			$sort = str_replace('_new_','_old_',$sort);  
			$order = isset($get_order) ? strval($get_order) : 'asc';  //默认排序
			$result = M();
			$user_table = C('DB_PREFIX').'user_table';
			$user_main = C('DB_PREFIX').'user_main_table';
			$part_table = C('DB_PREFIX').'user_part_table';
			$comy_table = C('DB_PREFIX').'user_company_table';
			$group_table = C('DB_PREFIX').'user_group_table';
			
			$map = array();
			if(cookie('user') || cookie('auser')){
				if(cookie('user')){
					$str_map = cookie('user');
					$map = unserialize($str_map);
				}else{
					$str_map = cookie('auser');
					$map = unserialize($str_map);
				}
				unset($str_map);
			}else{
				$map['id'] ="id>0";
				cookie('user',serialize($map));
			}
			
			//dump(unserialize(cookie('user')));
			$map = implode($map,' ');
			
			$get_page = $this->_get('page');
			$get_rows = $this->_get('rows');
			$page = isset($get_page) ? intval($get_page) : 1;    
			$rows = isset($get_rows) ? intval($get_rows) : $page_row; 
			$now_page = $page-1;
			$offset = $now_page*$rows;
			
			if(strstr($sort,'login_count') || strstr($sort,'id')){
				$new_order = $sort.' '.$order;
			}else{
				$new_order = 'convert('.$sort.' using gbk) '.$order;
			}
			
			$arr_flelds = array(
				'id' => 't1.id as id',
				'name' => 't1.username as username',
				'email' => 't1.email as email',
				'login_count' => 't1.login_count as login_count',
				'last_visit' => 't1.last_visit as last_visit',
				'status' => 't1.status as t1_old_status',
				'status2' => 'IF(t1.status=1,\'开启\',\'关闭\') as t1_new_status',
				'group_id' => 't5.group_id as group_id',
				'group' => 't2.name as group_name',
				'part' => 't3.name as part_name',
				'comy' => 't4.name as comy_name',
				'type' => 't4.type as type',
			);
			
			
			$fields = implode(',',$arr_flelds);
			if(!$view){//不开启视图
				$info = $result->table($user_table.' as t1')->field('SQL_CALC_FOUND_ROWS '.$fields)->join(' '.$user_main.' as t5 on t5.user_id = t1.id')->join(' '.$group_table.' as t2 on t2.id = t5.group_id')->join(' '.$part_table.' as t3 on t3.id = t5.part_id')->join(' '.$comy_table.' as t4 on t4.id = t5.company_id')->having($map)->order($new_order)->limit($offset,$rows)->select();
				$count = $result->query('SELECT FOUND_ROWS() as total');
				$count = $count[0]['total'];
			}else{//开启视图
				$info = $result->table($user_table.' as t1')->field($fields)->join(' '.$user_main.' as t5 on t5.user_id = t1.id')->join(' '.$group_table.' as t2 on t2.id = t5.group_id')->join(' '.$part_table.' as t3 on t3.id = t5.part_id')->join(' '.$comy_table.' as t4 on t4.id = t5.company_id')->having($map)->order($new_order)->select();
				$count = count($info);
			}
			//dump($info);exit;
			$new_info = array();
			$items = array();
			$new_info['total'] = $count;
			if($method=='total'){
				echo  json_encode($new_info); exit;
			}
			foreach($info as $t){
				if($t['last_visit']==0){
					$t['last_visit'] = '0000-00-00 00:00:00';
				}else{
					$t['last_visit'] = date("Y-m-d H:i:s",$t['last_visit']);
				}
				
				if($t['type']==1 && !C('MORE_COMY')){
					$t['part_name'] = $t['comy_name'].'（客户）';
				}
				
				if($t['status']==1){
					$t['status'] = '开启';
				}else{
					$t['status'] = '关闭';
				}
				
				if($t['report']==1){
					$t['report'] = '否';
				}else{
					$t['report'] = '是';
				}
				$items[] = $t;
			}
			
			//$items = array_sort($items,$sort,$mode='nokeep',$type=$order);
			
			$new_info['rows'] = $items;
			//dump($new_info);
			echo json_encode($new_info);
			
			unset($new_info,$info,$comy,$order,$sort,$count,$items);
		}else{
			$this->assign('page_row',$page_row);
			$this->display();
			unset($Public);
		}
    }
	
	
	/**
		* 新增与更新数据
		*@param $act add为新增、edit为编辑
		*@param $go  为1时，获取post
		*@param $id  传人数据id
		*@examlpe 
	*/
	public function add($act=NULL,$go=false,$id=NULL){		
		//main
		$user = D('User_table');
		if($go==false){
			$this->assign('uniqid',uniqid());
			if($act=='add'){
				$this->assign('act','add');
				$this->display();
			}else{
				$userid = $_SESSION['login']['se_id'];
				$userid = intval($userid);
				if(!is_int((int)$id)){
					$id = NULL;
					$this->show('无法获取ID');
				}else{
					$map['id'] = array('eq',$id);
					$info = $user->relation('user_main')->where($map)->find();
					$comy = D('User_company_table');
					$type = $comy->where('id='.$info['user_main']['company_id'])->getField('type');
					if($type==1 && !C('MORE_COMY')){
						$info['user_main']['part_id'] = '100'.$info['user_main']['company_id'];
					}
					unset($map);
					//dump($info);
					$this->assign('userid',$userid);
					$this->assign('id',$id);
					$this->assign('act','edit');
					$this->assign('info',$info);
					$this->display();
					unset($info);
				}
			}	
		}else{
			$data = $user->create();
			$data['date_created'] = time();
			if($data['realname']==''){
				$data['realname'] = $data['username'];
			}
			$data['user_main'] = array(
				'part_id'=>I('part_id'),
				'company_id'=>I('company_id'),
				'group_id'=>I('group_id'),
			);
			//dump($data);exit;
			if($act=='add'){
				$Public = A('Index','Public');
				$role = $Public->check('User',array('c'));
				if($role<0){
					echo $role; exit;
				}
				
				if($data['password']){
					$oldpwd = $data['password'];
					$data['password'] = md5($data['password']);
				}else{
					$rand_pwd = randnum(6);
					$oldpwd = $rand_pwd;
					$data['password'] = md5($rand_pwd);
				}
				
				if(strstr($data['user_main']['part_id'],'100')){
					$data['user_main']['company_id'] = substr($data['user_main']['part_id'],2,strlen($data['user_main']['part_id']));
					$data['user_main']['part_id'] = 0;
				}
				
				if(C('USER_TO_MAIL')){
					$Mailer = A('Mail','Public');
					$to = $data['email'];
					$title = '号码分派通知';
					$name = $data['username'];
					$notes = $data['username'];
					$mail_cfg = $Public->MC('sys');
					$host = C('CFG_HOST');
					$contents = '<p><span style="color: rgb(51, 51, 51); font-family: verdana, Tahoma, Arial, 宋体, sans-serif; font-size: 14px; ">您好：</span></p><p><span style="color: rgb(51, 51, 51); font-family: verdana, Tahoma, Arial, 宋体, sans-serif; font-size: 14px; ">管理員已为你分派了一个新的账号</span></p><p><span style="color: rgb(51, 51, 51); font-family: verdana, Tahoma, Arial, 宋体, sans-serif; font-size: 14px; ">账号：'.$name.' &nbsp; &nbsp; &nbsp; &nbsp;密码：'.$oldpwd.'</span></p><p><span style="color: rgb(51, 51, 51); font-family: verdana, Tahoma, Arial, 宋体, sans-serif; font-size: 14px; ">登录地址：</span><a target="_blank" href="'.$host.'">'.$host.'</a></p><p><span style="color: rgb(51, 51, 51); font-family: verdana, Tahoma, Arial, 宋体, sans-serif; font-size: 14px; ">此邮件由系統自动发送，请不要回复，如有问题，请联系系統管理员！</span></p>';
					$send = $Mailer->set($title,$contents,$mail_cfg);
					$Mailer->mailObj->AddAddress($to, $notes);
					$send = $Mailer->mailObj->send();
					$Mailer->mailObj->ClearAddresses();
					if($send==1){
						$mail = 1;
					}else{
						$mail = $Mailer->mailObj->ErrorInfo;
					}
					$Mailer->mailObj->ClearAddresses();
				}else{
					$mail = 1;
				}
				if($mail==1){
					$add = $user->relation(true)->add($data);
					if($add>0){
						echo 1;
						$this->json(NULL);
					}else{
						echo 0;
					}
				}else{
					echo 2;
				}
				unset($data,$Public);
			}elseif($act=='edit'){
				$Public = A('Index','Public');
				$role = $Public->check('User',array('u'));
				if($role<0){
					echo $role; exit;
				}
				
				if(!is_int((int)$id)){
					echo 0;
				}else{
					if($data['password']){
						$data['password'] = md5($data['password']);
					}else{
						unset($data['password']);
					}
					
					if(strstr($data['user_main']['part_id'],'100')){
						$data['user_main']['company_id'] = substr($data['user_main']['part_id'],2,strlen($data['user_main']['part_id']));
						$data['user_main']['part_id'] = 0;
					}
					
					$map['id'] = array('eq',$id);
					$edit = $user->relation(true)->where($map)->save($data);
					unset($map);
					if($edit !== false){
						$this->json(NULL);
						echo 1;
					}else{
						echo 0;
					}
					unset($data,$Public);
				}
			}
		}
		unset($user);
	}
	
	
	/**
		* 删除数据
		*@examlpe 
	*/
	public function del(){
		$Public = A('Index','Public');
		$role = $Public->check('User',array('d'));
		if($role<0){
			echo $role; exit;
		}
		
		//main
		$str_id = I('id');
		$str_id = strval($str_id);
		$str_id = substr($str_id,0,-1);
		$arr_id = explode(',',$str_id);
		$user = M('User_table');
		$pass = 0;$fail = 0;
		foreach($arr_id as $id){
			$map['id'] = array('eq',$id);
			$del = $user->where($map)->delete();
			if($del){
				$pass++;
			}else{
				$fail++;
			}
		}
		unset($map,$str_id,$arr_id);
		if($pass==0){
			echo 0;
		}else{
			$this->json(NULL);
			echo 1;
		}
		$pass = 0; $fail = 0;
		unset($user,$Public);
	}
	
	/**
		* 更改用户密码
		*@param $go  为1时，获取post
		*@param $id  传人数据id
		*@examlpe 
	*/
	public function repwd($id,$go=false){		
		//main
		$user = D('User_table');
		if(!$go){
			if(!is_int((int)$id)){
				$id = NULL;
				$this->show('无法获取ID');
			}else{
				$map['id'] = array('eq',$id);
				$info = $user->relation(true)->where($map)->find();
				unset($map);
				$this->assign('id',$id);
				$this->assign('info',$info);
				$this->display();
				unset($info);
			}		
		}else{
			$data = $user->create();
			if(!is_int((int)$id)){
				echo 0;
			}else{
				$pwd2 = I('password2');
				if($data['password']!=$pwd2){
					echo -1;
				}else{
					$data['password'] = md5($data['password']);
					$map['id'] = array('eq',$id);
					$edit = $user->where($map)->save($data);
					unset($map);
					if($edit !== false){
						echo 1;
					}else{
						echo 0;
					}
				}
			}
			unset($data);
		}
		unset($user);	
	}
	
	/**
		* 设置邮箱密码
		*@param $go  为1时，获取post
		*@param $id  传人数据id
		*@examlpe 
	*/
	public function setpwd($id,$go=false){		
		//main
		$user = D('User_table');
		$comy = D('User_company_table');
		if(!$go){
			if(!is_int((int)$id)){
				$id = NULL;
				$this->show('无法获取ID');
			}else{
				$map['id'] = array('eq',$id);
				$info = $user->relation(true)->where($map)->find();
				$cinfo = $comy->where('id='.$info['user_main']['company_id'])->find();
				if(C('MAIL_OF_USER') || $cinfo['type']==1){
					if(!$info['smtp']){
						if(C('MORE_COMY')){
							$info['smtp'] = $cinfo['smtp'];
							$info['ssl'] = $cinfo['ssl'];
							$info['port'] = $cinfo['port'];
						}else{
							$info['smtp'] = C('MAIL_SMTP_SEAVER');
							$info['ssl'] = C('MAIL_SMTP_SSL');
							$info['port'] = C('MAIL_SMTP_PORT');
						}
					}
				}else{
					if($id>1 || ($id==1 && !$info['smtp'])){
						if(C('MORE_COMY')){
							$info['smtp'] = $cinfo['smtp'];
							$info['ssl'] = $cinfo['ssl'];
							$info['port'] = $cinfo['port'];
						}else{
							$info['smtp'] = C('MAIL_SMTP_SEAVER');
							$info['ssl'] = C('MAIL_SMTP_SSL');
							$info['port'] = C('MAIL_SMTP_PORT');
						}
					}
				}
				unset($map);
				$this->assign('id',$id);
				$this->assign('info',$info);
				$this->display();
				unset($info);
			}		
		}else{
			if(!is_int((int)$id)){
				echo 0;
			}else{
				$map['id'] = array('eq',$id);
				$info = $user->relation(true)->where($map)->find();
				$cinfo = $comy->where('id='.$info['user_main']['company_id'])->find();
				//dump($cinfo);
				$email = I('email');
				if(C('MAIL_OF_USER') || $cinfo['type']==1){
					$smtp = I('smtp');
					$ssl = I('ssl');
					$port = I('port');
				}
				$MailPwd2 = I('MailPwd2');
				$MailPwd = I('MailPwd');
				if($MailPwd!=$MailPwd2){
					echo -1;
				}else{
					$data['email'] = $email;
					$data['MailPwd'] = $MailPwd;
					if(C('MAIL_OF_USER') || $cinfo['type']==1){			
						$data['smtp'] = $smtp;
						$data['ssl'] = $ssl;
						$data['port'] = $port;
					}else{
						if($id==1){
							$data['smtp'] = $smtp;
							$data['ssl'] = $ssl;
							$data['port'] = $port;
						}
					}
					//dump($data);
					$edit = $user->where($map)->save($data);
					unset($map);
					if($edit !== false){
						echo 1;
					}else{
						echo 0;
					}
				}
			}
			unset($data);
		}
		unset($user);	
	}
	
	//无效方法
	public function setmail(){		
		//main
		$user = D('User_table');
		$mailpwd = I('mailpwd');
		$userid = I('id');
		$mailpwd = strval($mailpwd);
		$data = array(
			'MailPwd'=>$mailpwd
		);
		$edit = $user->where('id='.$userid)->save($data);
		if($edit==1){
			echo 1;
		}else{
			echo 0;
		}
		unset($user,$data);	
	}
	
	
	/**
		* 重置用户密码
		*@param $id  传人数据id
		*@examlpe 
	*/
	public function rspwd($id){
		$Public = 	A('Index','Public');
		$Mailer = A('Mail','Public');
			
		//main
		$user = D('User_table');
		if(!is_int((int)$id)){
			echo 0;
		}else{
			$rand_pwd = randnum(6);
			$data['password'] = md5($rand_pwd);
			$map['id'] = array('eq',$id);
			$info = $user->where($map)->find();
			$edit = $user->where($map)->save($data);
			
			if($edit !== false){
				$to = $info['email'];
				$title = '重置密码通知';
				$name = $info['username'];
				$notes = 'Dear '.$info['username'];
				$mail_cfg = $Public->MC('sys');
				$host = C('CFG_HOST');
				$contents = '<p><span style="color: rgb(51, 51, 51); font-family: verdana, Tahoma, Arial, 宋体, sans-serif; font-size: 14px; ">您好：</span></p><p><span style="color: rgb(51, 51, 51); font-family: verdana, Tahoma, Arial, 宋体, sans-serif; font-size: 14px; ">您在项目管理系統的密码已被重置</span></p><p><span style="color: rgb(51, 51, 51); font-family: verdana, Tahoma, Arial, 宋体, sans-serif; font-size: 14px; ">账号：'.$name.' &nbsp; &nbsp; &nbsp; &nbsp;密码：'.$rand_pwd.'</span></p><p><span style="color: rgb(51, 51, 51); font-family: verdana, Tahoma, Arial, 宋体, sans-serif; font-size: 14px; ">登錄地址：</span><a target="_blank" href="'.$host.'">'.$host.'</a></p><p><span style="color: rgb(51, 51, 51); font-family: verdana, Tahoma, Arial, 宋体, sans-serif; font-size: 14px; ">此邮件由系统自动发送，请不要回复，如有问题请联系系统管理员！</span></p>';
				$send = $Mailer->set($title,$contents,$mail_cfg);
				$Mailer->mailObj->AddAddress($to, $notes);
				$send = $Mailer->mailObj->send();
				$Mailer->mailObj->ClearAddresses();
				unset($Mailer,$m_cfg,$notes,$name,$to,$title,$contents,$data);
				if($send==1){
					echo 1;
				}else{
					$data['password'] = $info['password'];
					$map['id'] = array('eq',$id);
					$edit = $user->where($map)->save($data);
					echo 2;
				}
			}else{
				echo 0;
			}
		}
		unset($data,$user,$map,$info);
	}
	
	/**
		* 高级搜索
		*@param $act   为1时，获取post
		*@examlpe 
	*/
	public function advsearch($act=NULL){
		$App = A('App','Public');
			
		//main
		$field = strval($field);
		if($act==1){
			$field = I('field');
			$mod = I('mod');
			$keyword = I('keys');	
			$type = I('type');
			array_pop($field); array_pop($mod); array_pop($keyword); array_pop($type);
			
			$del = array_pop($type);
			
			$arr = array();
			$num = 0;
			$map['id'] ='id>0';
			foreach($field as $key=>$val){
				if($mod[$key]=='like' || $mod[$key]=='notlike'){
					$keyword[$key] = '%'.$keyword[$key].'%';
				}
				$tt = trim($type[$key]);
				$n = $key+1;
				$l = $key-1;
				$nt = trim($type[$n]);
				$lt = trim($type[$l]);
				$lf = $field[$l];
				$step = 1;
				
				if($val==$lf){
					$str = $val.$step;
					$step++;
				}else{
					$str = $val;
				}
				
				if($tt=='OR'){
					if($keyword[$key]){
						$mod[$key] = htmlspecialchars_decode($mod[$key]);
						$arr[$num]['k'][] = $val;
						$arr[$num]['v'][] = $val." ".$mod[$key]." '".$keyword[$key]."'";
					}
					if($nt=='AND'){
						$mod[$n] = htmlspecialchars_decode($mod[$n]);
						if($mod[$n]=='like' || $mod[$n]=='notlike'){
							$keyword[$n] = '%'.$keyword[$n].'%';
						}
						if($keyword[$n]){
							$arr[$num]['k'][] = $val;
							$arr[$num]['v'][] = $val." ".$mod[$n]." '".$keyword[$n]."'";
						}
						$num++;
					}
				}else{
					if($lt!='OR' && $tt=='AND'){
						$mod[$key] = htmlspecialchars_decode($mod[$key]);
						if($keyword[$key]){
							$map[$str] = ' and '.$val." ".$mod[$key]." '".$keyword[$key]."'";
						}
					}
				}
				
				if(!isset($type[$key]) && $lt=='OR'){
					$mod[$key] = htmlspecialchars_decode($mod[$key]);
					if($keyword[$key]){
						$arr[$num]['k'][] = $val;
						$arr[$num]['v'][] = $val." ".$mod[$key]." '".$keyword[$key]."'";
					}
				}else{
					if(!isset($type[$key]) && $lt!='OR'){
						$mod[$key] = htmlspecialchars_decode($mod[$key]);
						if($keyword[$key]){
							$map[$str] = ' and '.$val." ".$mod[$key]." '".$keyword[$key]."'";
						}
					}
				}
			}
			$num = 0;
			unset($key,$val,$ntable,$table,$field,$mod,$type,$keyword);
			
			foreach($arr as $key=>$val){
				$str = $val['k'][0];
				for($i=0;$i<count($val['v']);$i++){
					if($i==0){
						$map[$str] .= ' and ('.$val['v'][$i];
					}elseif($i==count($val['v'])-1){
						$map[$str] .= ' or '.$val['v'][$i].')';
					}else{
						$map[$str] .= ' or '.$val['v'][$i];
					}
				}	
			}
			unset($arr);
			
			cookie('user',NULL);
			cookie('auser',serialize($map));
			echo 1;
			unset($map);
		}else{
			$this->assign('uniqid',uniqid());
			$this->assign('field',$field);
			$this->display();
		}	
	}
	
	
	/**
		* 清空所以搜索产生的cookies
		*@examlpe 
	*/
	public function clear(){
    	cookie('user',NULL);
		cookie('auser',NULL);
	}
	
	/**
		* 生成json文件
		*@param $back  为1时，返回数据
		*@examlpe 
	*/
	public function json($back=1){
		$Write = A('Write','Public');
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
	
		//main
    	$temp_path = RUNTIME_PATH.'/Temp/';
		if(file_exists($temp_path)){
			$dt = $sys->delFile($temp_path);
		}
		$result = M();
		$user = M('User_table');
		$group = M('User_group_table');
		$user_table = C('DB_PREFIX').'user_table';
		$main_table = C('DB_PREFIX').'user_main_table';
		$path = RUNTIME_PATH.'Data/Json';
		
		$ginfo = $group->field('CONCAT(\'top_\',id) as id,name as text,\'open\' as state')->where('status=1 or id=1')->order('access desc')->select();
		$infos = $result->table($user_table.' as t1')->field('t1.id,t1.username as text,t2.group_id')->join(' join '.$main_table.' as t2 on t1.id=t2.user_id')->where('t1.status=1')->order('convert(text using gbk) asc')->select();
		$new_info = array();
		foreach($ginfo as $k=>$t){
			$gid = str_replace('top_','',$t['id']);
			$infos = $result->table($user_table.' as t1')->field('t1.id,t1.username as text,t2.group_id')->join(' join '.$main_table.' as t2 on t1.id=t2.user_id')->where('t1.status=1 and t2.group_id='.$gid)->order('convert(text using gbk) asc')->select();
			$ginfo[$k]['children'] = $infos;
		}
		$json_data = json_encode($ginfo);
		$put_json5 = $Write->write($path,$json_data,'User_tree_data');
		
		$info = $user->field('id,username as text')->where('status=1')->order('convert(text using gbk) asc')->select();
		//array_unshift($info,$head);
		$json_data = json_encode($info);
		//dump($info);
		$path = RUNTIME_PATH.'Data/Json';
		$put_json = $Write->write($path,$json_data,'User_data');
		
		$info = $user->field('id as id,username as text')->where('status=1')->order('convert(text using gbk) asc')->select();
		$head = array(
			'id'=>0,
			'text'=>'无'
		);
		array_unshift($info,$head);
		$json_data = json_encode($info);
		$put_json2 = $Write->write($path,$json_data,'User_2_data');
		
		$info = $user->field('username as id,username as text')->where('status=1')->order('convert(text using gbk) asc')->select();
		$json_data = json_encode($info);
		$put_json4 = $Write->write($path,$json_data,'User_name_data');
		
		$info = $user->field('id,username as text')->where('status=1 and id<>1')->order('convert(text using gbk) asc')->select();
		//array_unshift($info,$head);
		$json_data = json_encode($info);
		$put_json3 = $Write->write($path,$json_data,'User_noadmin_data');
		
		if($back==1){
			if($put_json){
				echo 1;
			}else{
				echo 0;
			}
		}
		unset($info,$json_data,$path,$Loop,$Write,$sys);
	}
	
	/**
		* 工具栏搜索控制
		*@param $act  传入的字段名
		*@examlpe 
	*/
	public function change($act){
		$val = I('val');
		
		if(strstr($val,'top_')){
			$val = str_replace('top_','',$val);
			$map['id'] ="id>0";
			$map['group_id'] = ' and group_id='.$val;
		}else{
			$map['id'] = 'id='.$val;
		}
		cookie('user',serialize($map));
	}
}