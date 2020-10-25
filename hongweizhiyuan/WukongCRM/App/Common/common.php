<?php
function deldir($dir) {
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (!is_dir($fullpath)) {
                @unlink($fullpath);
            } else {
                @deldir($fullpath);
            }
        }
    }
    closedir($dh);
}

//手机端选择负责人
function owner_name_select($role_id){
	$d_role_view = D('RoleView');
		
	$all_role = M('role')->where('user_id <> 0')->select();
	$below_role = getSubRole(session('role_id'), $all_role);

	$below_ids[] = session('role_id');
	foreach ($below_role as $key=>$value) {
		$below_ids[] = $value['role_id'];
	}
	$where = 'role.role_id in ('.implode(',', $below_ids).')';
	
	$role_list = $d_role_view->where($where)->order('department_id ASC,position_id ASC')->field('role_id,user_name,department_name,role_name')->select();
	
	$string = '<select id="owner_role_id" name="owner_role_id">';
	if(is_array($role_list)){
		$string .= '<option value="0">请选择负责人</option>';
		foreach($role_list as $v){
			if($role_id && $role_id == $v['role_id']){
				$string .= '<option selected="selected" value="'.$v['role_id'].'">'.$v['user_name'].'</option>';
			}else{
				$string .= '<option value="'.$v['role_id'].'">'.$v['user_name'].'</option>';
			}			
		}		
	}
	$string .= '</select>';
	return $string;
}

//手机端客户选择联系人
function customer_name_select($list,$role_id){
	$string = '<select id="owner_role_id" name="owner_role_id">';
	if(is_array($role_list)){
		$string .= '<option value="0">--请选择负责人--</option>';
		foreach($role_list as $v){
			if($role_id && $role_id == $v['role_id']){
				$string .= '<option selected="selected" value="'.$v['role_id'].'">'.$v['user_name'].' - '.$v['department_name'].'</option>';
			}else{
				$string .= '<option value="'.$v['role_id'].'">'.$v['user_name'].' - '.$v['department_name'].'</option>';
			}			
		}		
	}
	$string .= '</select>';
	return $string;
}

//高级搜索生成where条件
function field($search,$condition=''){
	switch ($condition) {
		case "is" : $where = array('eq',$search);break;
		case "isnot" :  $where = array('neq',$search);break;
		case "contains" :  $where = array('like','%'.$search.'%');break;
		case "not_contain" :  $where = array('notlike','%'.$search.'%');break;
		case "start_with" :  $where = array('like',$search.'%');break;
		case "not_start_with" :  $where = array('notlike',$search.'%');break;
		case "end_with" :  $where = array('like','%'.$search);break;
		case "is_empty" :  $where = array('eq','');break;
		case "is_not_empty" :  $where = array('neq','');break;
		case "gt" :  $where = array('gt',$search);break;
		case "egt" :  $where = array('egt',$search);break;
		case "lt" :  $where = array('lt',$search);break;
		case "elt" :  $where = array('elt',$search);break;
		case "eq" : $where = array('eq',$search);break;
		case "neq" : $where = array('neq',$search);break;
		case "between" : $where = array('between',array($search-1,$search+86400));break;
		case "nbetween" : $where = array('not between',array($search,$search+86399));break;
		case "tgt" :  $where = array('gt',$search+86400);break;
		default : $where = array('eq',$search);
	}
	return $where;
}

function format_price($num){
	$num = round($num, 0);
	$s_num = strval($num);
	$len = strlen($s_num)-1;
	$result = round($num, -$len);
	return $result;
}

//方法说明	获取首页需要显示的列名字符串
function getIndexFields($model){
    if(!$model) return false;
	$m_model = M($model);
	$where['in_index'] = 1;
	$where['model'] = $model;
	$model_fields = M('Fields')->where($where)->order('order_id ASC')->select();
	return $model_fields;
}
//获取主表字段 用于搜索
function getMainFields($model){
	if(!$model) return false;
	$m_model = M($model);
	$where['is_main'] = 1;
	$where['model'] = $model;
	$model_fields = M('Fields')->where($where)->order('order_id ASC')->select();
	return $model_fields;
}
/**记录操作日志
 * $id 操作对象id
 * $param_name 参数
 * $text 附加信息
 * 2013-10-23
 **/
function actionLog($id,$param_name='',$text=''){
    $role_id = session('role_id');
    $user = M('user')->where(array('user_id'=>session('user_id')))->find();
    $category = $user['category_id'] == 1 ? L('ADMIN') : L('USER');
    $data['role_id'] = $role_id;
    $data['module_name'] = strtolower(MODULE_NAME);
    $data['action_name'] = strtolower(ACTION_NAME);
	if(!empty($param_name)){
		$data['param_name'] = strtolower($param_name);
	}
    $data['create_time'] = time();
    $data['action_id'] = $id;
    $data['content'] = L('ACTIONLOG',array($category,$user['name'],date('Y-m-d H:i:s'),L(ACTION_NAME),$id,L(MODULE_NAME),$text));
    $actionLog = M('actionLog');
    $actionLog->create($data);
    if($actionLog->add()) return true;
    return false;
}
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

function getDateTime($model){		
	$user = M('User')->where('role_id = %d',session('role_id'))->field('user_id,last_read_time')->find();		
	if($user['last_read_time']){
		$last_read_time = json_decode($user['last_read_time'],true);			
	}
	$last_read_time[$model] = time();	
	$last_read_time = json_encode($last_read_time);		
	M('User')->where('user_id = %d',$user['user_id'])->setField('last_read_time',$last_read_time);
	return true;
}

function getSubCategory($category_id, $category, $separate) {
	$array = array();
	foreach($category AS $value) {
		if ($category_id == $value['parent_id']) {
			$array[] = array('category_id' => $value['category_id'], 'name' => $separate.$value['name'],'description'=>$value['description']);
			$array = array_merge($array, getSubCategory($value['category_id'], $category, $separate.'--'));
		}
	}
	return $array;
}

// 不包括自己所在部门
function getSubDepartment($department_id, $department, $separate, $no_separater) {
	$array = array();
	if($no_separater){
		foreach($department AS $value) {
			if ($department_id == $value['parent_id']) {
				$array[] = array('department_id' => $value['department_id'], 'name' => $separate.$value['name'],'description'=>$value['description']);
				$array = array_merge($array, getSubDepartment($value['department_id'], $department, $separate, 1));
			}
		}
	}else{
		foreach($department AS $value) {
			if ($department_id == $value['parent_id']) {
				$array[] = array('department_id' => $value['department_id'], 'name' => $separate.$value['name'],'description'=>$value['description']);
				$array = array_merge($array, getSubDepartment($value['department_id'], $department, $separate.'--'));
			}
		}
	}
	return $array;
}

//包括自己所在部门
function getSubDepartment2($department_id, $department, $first=0) {
	$array = array();
	$m_department =  M('role_department');
	if($first == 1){
		$depart = $m_department->where('department_id = %d', session('department_id'))->find();
		$array[] = array('department_id'=>$depart['department_id'],'name'=>$depart['name'], 'description'=>$depart['description']);
	}
	foreach($department AS $value) {
		if ($department_id == $value['parent_id']) {
			$array[] = array('department_id' => $value['department_id'], 'name' => $separate.$value['name'],'description'=>$value['description']);
			$array = array_merge($array, getSubDepartment2($value['department_id'], $department, '--'));
		}
	}
	return $array;
}

function getSubDepartmentTreeCode($department_id, $first=0) {
	$string = "";
	$department_list = M('roleDepartment')->where('parent_id = %d', $department_id)->select();
	$position_list = M('position')->where('department_id = %d', $department_id)->select();

	if ($department_list || $position_list) {
		if ($first) {
			$string = '<ul id="browser" class="filetree">';
		} else {
			$string = "<ul>";
		}
		

		foreach($position_list AS $value) {
			$string .= "<li><span rel='".$value['position_id']."' class='file'>".$value['name']." &nbsp; <span class='control' id='control_file".$value['position_id']."'><a class='position_edit' rel=".$value['position_id']." href='javascript:void(0)'>".L('EDIT')."</a> &nbsp; <a class='position_delete' rel=".$value['position_id']." href='javascript:void(0)'>".L('DELETE')."</a> </span> </span></li>";
		}
		foreach($department_list AS $value) {
			if($first){
				$string .= "<li><span rel='".$value['department_id']."' class='folder'>".$value['name']." &nbsp; <span class='control' id='control_folder".$value['department_id']."'><a class='department_edit' rel=".$value['department_id']." href='javascript:void(0)'>".L('EDIT')."</a> &nbsp; <a class='department_delete' rel=".$value['department_id']." href='javascript:void(0)'>".L('DELETE')."</a> </span></span>".getSubDepartmentTreeCode($value['department_id'])."</li>";
			} else {
				$string .= "<li class='closed'><span rel='".$value['department_id']."' class='folder'>".$value['name']." &nbsp; <span class='control' id='control_folder".$value['department_id']."'><a class='department_edit' rel=".$value['department_id']." href='javascript:void(0)'>".L('EDIT')."</a> &nbsp; <a class='department_delete' rel=".$value['department_id']." href='javascript:void(0)'>".L('DELETE')."</a> </span></span>".getSubDepartmentTreeCode($value['department_id'])."</li>";
			}
			
		}
		$string .= "</ul>";
	} 

	return $string;
}
//type == 1获取授权完整树形图
//type == 2获取选择授权树形图
function getSubPositionTreeCode($position_id, $first=0,  $type=1) {
	$string = "";
	$position_list = M('position')->where('parent_id = %d', $position_id)->select();

	if ($position_list) {
		if ($first) {
			if($type == 1)
				$string = '<ul id="browser" class="filetree">';
			else 
				$string = '<ul class="filetree">';
		} else {
			$string = "<ul>";
		}
		foreach($position_list AS $value) {
			$department_name = M('RoleDepartment')->where('department_id = %d', $value['department_id'])->getField('name');
			$user_list = D('RoleView')->where('position.position_id = %d', $value['position_id'])->select();
			$user_str = '';
			foreach($user_list as $v){
				if($v['status'] == '0'){
					$username = $v['user_name'].'-未激活';
				}elseif($v['status'] == '2'){
					$username = '<del>'.$v['user_name'].'</del>';
				}else{
					$username = $v['user_name'];
				}
				$user_str .= '<a style="color: #000000;" href="'.U('user/view','id='.$v['user_id']).'" target="_blank">'.$username.'、</a>';
			}
			if($user_str) $user_str = '('.$user_str.')';
			
			if($type == 1){
				$link_str = " <span class='control' id='control_file".$value['position_id']."'> <a class='position_edit' rel=".$value['position_id']." href='javascript:void(0)'>".L('EDIT')."</a> &nbsp; <a class='permission' rel=".$value['position_id']." href='javascript:void(0)'>".L('AUTHORIZE')."</a> &nbsp; <a class='position_delete' rel=".$value['position_id']." href='javascript:void(0)'>".L('DELETE')."</a></span>";
			}else{
				//$link_str = " <span class='control' id='control_file".$value['position_id']."'> <a class='allow_permission_id' rel=".$value['position_id']." href='javascript:void(0)'>".'选择'."</a> ";
				$link_str = " <span class='control' id='control_file".$value['position_id']."'> <input class='allow_permission_id' type='radio' name='parent_id' rel=".$value['position_id']." href='javascript:void(0)'>";
			}
			
			$string .= "<li style='list-style-type: none;'><span rel='".$value['position_id']."' class='file'>".$value['name']." - $department_name"." &nbsp; ".$user_str." &nbsp;".$link_str."</span>".getSubPositionTreeCode($value['position_id'], 0, $type)."</li>";
			
		}
		$string .= "</ul>";
	} 

	return $string;
}

function getSubRoleId($self = true){
	$all_role = M('role')->where('user_id <> 0')->select();
	$below_role = getSubRole(session('role_id'), $all_role);
	$below_ids = array();
	if ($self) {
		$below_ids[] = session('role_id');
	}
	foreach ($below_role as $key=>$value) {
		$below_ids[] = $value['role_id'];
	}
	return $below_ids;
}


/*
*	手机端getSubRoleId
*/
function getSubRoleByRole($role_id,$self = true){
	$all_role = M('role')->where('user_id <> 0')->select();
	$below_role = getSubRole($role_id, $all_role);
	$below_ids = array();
	if ($self) {
		$below_ids[] = $role_id;
	}
	foreach ($below_role as $key=>$value) {
		$below_ids[] = $value['role_id'];
	}
	return $below_ids;
}

//原获取职位列表方法
function getSubRole($role_id, $role_list, $separate) {
	$d_role = D('RoleView');
	if($d_role->where('role.role_id = %d', $role_id)->find()){
		$position_id = $d_role->where('role.role_id = %d', $role_id)->getField('position_id');
	}else{
		$position_id  = 0;
	}
	$sub_position = getPositionSub($position_id ,true);
	foreach($sub_position AS $position_id) {
		$son_role = $d_role->where('role.position_id = %d', $position_id['position_id'])->select();
		foreach($son_role as $val){
			$array[] = array('role_id' => $val['role_id'],'user_id' => $val['user_id'], 'parent_id' => $val['parent_id'], 'name' => $separate . $val['department_name'] . ' | ' . $val['role_name']);
		}
	}
	return $array;
}
//原获取下级职位列表方法
function getPositionSub($position_id ,$sub = false){
	$sub_position = M('position')->where('parent_id = %d', $position_id)->select();
	$array = $sub_position;
	if($sub){
		foreach($sub_position as $value){
			$son_position = getPositionSub($value['position_id'] ,$sub);
			if(!empty($son_position)){
				$array = array_merge($array, $son_position);
			}
		}
	}
	return $array;
}

function getSubPosition($position_id, $position, $separate) {
	$array = array();
	foreach($position AS $key=> $value) {
		if ($position_id == $value['parent_id']) {
			$m_department = M('RoleDepartment');
			$department_name = $m_department->where('department_id = %d', $value['department_id'])->getField('name');
			$array[] = array('position_id' => $value['position_id'], 'name' => $separate . $department_name . ' | ' . $value['name'],'description'=>$value['description']);
			$array = array_merge($array, getSubPosition($value['position_id'], $position, $separate.' -- '));
		}
	}
	return $array;
}

function getSubDepartmentByRole($role_id = 0){
	if($role_id <= 0) $role_id = session('role_id');
	$department_id = M('Role')->where('role_id = %d', $role_id)->getField('department_id');
	//未完成方法
}
//通过部门id获取该部门员工
function getRoleByDepartmentId($department_id){
	$id_array = array($department_id);
	$departments = M('roleDepartment')->select();
	$roleList = D('RoleView')->where('position.department_id = %d and role.role_id in (%s)', $department_id, implode(',', getSubRoleId()))->select();
	foreach($departments AS $value) {
		if ($department_id == $value['parent_id']) {
			$id_array[] = $value['department_id'];
			$role_list = getRoleByDepartmentId($value['department_id']);
			if(!empty($role_list)){
				$roleList = array_merge($roleList, $role_list);
			}
		}
	}
	return $roleList;
}
/**
 * Warning提示信息
 * @param string $type 提示类型 默认支持success, error, info
 * @param string $msg 提示信息
 * @param string $url 跳转的URL地址
 * @return void
 */
function alert($type='info', $msg='', $url='') {
    //多行URL地址支持
    $url        = str_replace(array("\n", "\r"), '', $url);
	$alert = unserialize(stripslashes(cookie('alert')));
    if (!empty($msg)) {
        $alert[$type][] = $msg;
		cookie('alert', serialize($alert));
	}
    if (!empty($url)) {
		if (!headers_sent()) {
			// redirect
			header('Location: ' . $url);
			exit();
		} else {
			$str    = "<meta http-equiv='Refresh' content='0;URL={$url}'>";
			exit($str);
		}
	}

	return $alert;
}

function parseAlert() {
	$alert = unserialize(stripslashes(cookie('alert')));
	cookie('alert', null);

	return $alert;
}

function getUserByRoleId($role_id){
	$role = D('RoleView')->where('role.role_id = %d', $role_id)->find();
	return $role;
}

function sendRequest($url, $params = array() , $headers = array()) {
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
	if (!empty($params)) {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	}
	if (!empty($headers)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$txt = curl_exec($ch);
	if (curl_errno($ch)) {
        $array[0] = 0;
        $array[1] = L("CONNECT TO A SERVER ERROR");
        $array[2] = -1;
		$return = $array;
	} else {
		$return = json_decode($txt, true);
		if (!$return) {
            $array[0] = 0;
            $array[1] = L("THE SERVER RETURNS DATA ANOMALIES");
            $array[2] = -1;
			$return = $array;
		}
	}

	return $return;
}

//生成评论提醒标题
function createCommentAlertInfo($module,$module_id){
	$author = D('RoleView')->where('role.role_id = %d', session('role_id'))->find();
	if($module == 'log'){
		$log = M('log')->where('log_id = %d', $module_id)->find();
		$title = L('LOG COMMENT TITLE',array($log['subject'],$author['user_name'],$author['department_name'],$author['role_name']));
	}elseif($module == 'task'){
		$task = M('task')->where('task_id = %d', $module_id)->find();
        $title = L('TASK COMMENT TITLE',array($task['subject'],$author['user_name'],$author['department_name'],$author['role_name']));
	}
	return $title;
}

//$sysMessage=0 为系统消息
function sendMessage($id,$content,$sysMessage=0,$weixin = 0){
	if(!$id) return false;
	if(!$content) return false;
	$m_message = M('message');
	if($sysMessage == 0) $data['from_role_id'] = session('role_id');
	$data['to_role_id'] = $id;
	$data['content'] = $content;
	$data['read_time'] = 0;
	$data['send_time'] = time();
	return $m_message->add($data);
}

/*
	功能:发送邮件
	参数说明：  $to_role_id 收件人role_id
				$title 邮件主题
				$content 邮件内容
				$author 作者
*/
function sysSendEmail($to_role_id,$title,$content,$author){
	C(F('smtp'),'smtp');
	if(!$content) return false;
	if(!$to_role_id) return false;
	if(!$author) $author=C('defaultinfo.name').L('ADMIN');
	import('@.ORG.Mail');
	$to_user = D('RoleView')->where('role.role_id = %d', $to_role_id)->find();
	if(!is_email($to_user['email'])) return false;
	return SendMail($to_user['email'],$title,$content,$author);
}
function userSendEmail($address,$title,$content,$author=false){
	C(F('smtp'),'smtp');
	if(!$address) return false;
	if(!$content) return false;
	$content = preg_replace('/\\\\/','', $content);
	$userid = session('user_id');
    $user = M('user')->where(array('user_id'=>$userid))->find();
	if($author===true) $author=C('defaultinfo.name').'-'.$user['name'];
	else $author=C('defaultinfo.name');
	import('@.ORG.Mail');
	if(!is_email($address)) return false;
	return SendMail($address,$title,$content,$author);
}


function bsendemail($address,$title,$content,$file=array(),$author=false,$selfsmtp=false){
	if(!$address) return false;
	if(!$content) return false;
	$content = eregi_replace("[\]",'',$content);
	$userid = session('user_id');
	$user = M('user')->where(array('user_id'=>$userid))->find();
	if($author===true) $author=C('defaultinfo.name').'-'.$user['name'];
	else $author=C('defaultinfo.name')."-wukong";

	if($selfsmtp){
		$smtp = M('UserSmtp')->where('smtp_id = %d', intval($selfsmtp))->find();
		C(unserialize($smtp['settinginfo']), 'smtp');
	}else{
		C(F('smtp'),'smtp');
	}
	
	import('@.ORG.Mail');
	$mail= new PHPMailer(true);
	try {
		$mail->IsSMTP();
		$mail->CharSet=C('MAIL_CHARSET');
		$mail->AddAddress($address);
		$mail->Body=$content;
		$mail->From= C('MAIL_ADDRESS');
		$mail->FromName=$author;
		$mail->Subject=$title;
		$mail->Host=C('MAIL_SMTP');
		$mail->SMTPAuth=C('MAIL_AUTH');
		$mail->Username=C('MAIL_LOGINNAME');
		$mail->Password=C('MAIL_PASSWORD');  
		$mail->IsHTML(C('MAIL_HTML'));
		$mail->MsgHTML($content);
		 ////对邮件正文进行重新编码，保证中文内容不乱码 如果正文引用该图片 就不会以附件形式存在 而是在正文中
		if(!empty($file)){
			foreach($file as $k=>$v){
				$mail->AddAttachment(ltrim($v,'/'));
			}
		}

		//$mail->AddAttachment($content); //上传附件内容
		return($mail->Send());
	} catch (phpmailerException $e) {
	 // echo $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
	  //echo $e->getMessage(); //Boring error messages from anything else!
	}
}

function sysSendSms($to_role_id,$content){

	if(!$content) return false;
	if(!$to_role_id) return false;
	if(!$title) $title="系统通知";
	if(!$author) $author=C('defaultinfo.name').L('ADMIN');

	$to_user = D('RoleView')->where('role.role_id = %d', $to_role_id)->find();
	if(!is_email($to_user['email'])) return 100;
	return sendSMS($to_user['telephone'],$content,'sign_sysname');
}

function isMobile(){

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");

    $is_mobile = false;

    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            $is_mobile = true;
            break;
        }
    }

    return $is_mobile;
}

function is_utf8($liehuo_net){
	if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$liehuo_net) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$liehuo_net) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$liehuo_net) == true) 
	{
		return true; 
	}
	else 
	{ 
		return false; 
	}
}

//验重二维数组排序  $arr 数组 $keys比较的键值
function array_sort($arr,$keys,$type='asc'){ 
	$keysvalue = $new_array = array();
	foreach ($arr as $k=>$v){
		$keysvalue[$k] = $v[$keys];
	}
	if($type == 'asc'){
		asort($keysvalue);
	}else{
		arsort($keysvalue);
	}
	reset($keysvalue);
	$i = 0;
	foreach ($keysvalue as $k=>$v){
		if($i < 8 && $arr[$k][search] > 0){
			$new_array[] = $arr[$k]['value'];
			$i++;
		}
		
	}
	return $new_array; 
}
//自定义字段html输出     $field为特殊验重字段   $d_module=($ModuelView) 
function field_list_html($type="add",$module="",$d_module=array(),$app=""){
	if ($type == "add") {
		$field_list = M('Fields')->where('model = "'.$module.'" and in_add = 1')->order('order_id')->select();
	} else {
		$field_list = M('Fields')->where('model = "'.$module.'"')->order('order_id')->select();
	}
	
	foreach($field_list as $k=>$v){
		if(trim($v['input_tips'])){
			$input_tips = ' &nbsp; <span style="color:#005580;">('.L('NOTE_').$v['input_tips'].')</span>';
		}else{
			$input_tips = '';
		}
		if('add' == $type){
			$value = $v['default_value'];
		} elseif ('edit' == $type && !empty($d_module)) {
			$value = $d_module[$v['field']] !== '' ? $d_module[$v['field']] : '';
		}
		
		if($d_module['customer_id']){
			$customer_id = intval($d_module['customer_id']);
		}else{
			$customer_id = intval($_GET['customer_id']);
		}
		
		if($customer_id){
			$customer = M('customer')->where('customer_id = %d', $customer_id)->find();
			$contacts = M('contacts')->where('contacts_id = %d', $customer['contacts_id'])->find();
		}
		if ($v['field'] == 'customer_id') {
			if($customer_id){
				if(!empty($app) && $app == 'App'){	
					$field_list[$k]['html'] = '<input type="hidden" name="'.$v['field'].'" id="customer_id" value="'.$customer['customer_id'].'"/><input  type="text" name="customer_name" id="customer_name" value="'.$customer['name'].'"/> <a target="_blank" href="'.U('AppCustomer/add').'">'.L('CREATE NEW CUSTOMER').'</a>';
				}else{		
					$field_list[$k]['html'] = '<input type="hidden" name="'.$v['field'].'" id="customer_id" value="'.$customer['customer_id'].'"/><input  type="text" name="customer_name" id="customer_name" value="'.$customer['name'].'"/> <a target="_blank" href="'.U('customer/add').'">'.L('CREATE NEW CUSTOMER').'</a>';
				}
			}else{				
				if(!empty($app) && $app == 'App'){						
					$field_list[$k]['html'] = '<input type="hidden" name="'.$v['field'].'" id="customer_id"/><input  type="text" name="customer_name" id="customer_name"> <a target="_blank" href="'.U('AppCustomer/add').'">'.L('CREATE NEW CUSTOMER').'</a>';
				}else{					
					$field_list[$k]['html'] = '<input type="hidden" name="'.$v['field'].'" id="customer_id"/><input  type="text" name="customer_name" id="customer_name"> <a target="_blank" href="'.U('customer/add').'">'.L('CREATE NEW CUSTOMER').'</a>';
				}				
			}
		}elseif($v['field'] == 'contacts_id'){
			if($customer_id){
				$field_list[$k]['html'] = '<input type="hidden" name="contacts_id" id="contacts_id" value="'.$contacts['contacts_id'].'"/><input  type="text" name="contacts_name" id="contacts_name" value="'.$contacts['name'].'"/>';
			}else{
				$field_list[$k]['html'] = '<input type="hidden" name="contacts_id" id="contacts_id"/><input  type="text" name="contacts_name" id="contacts_name"/>';
			}
		}else{
            switch ($v['form_type']) {
                case 'textarea' :
                    $field_list[$k]['html'] = '<textarea  rows="6" class="span6" id="'.$v['field'].'" name="'.$v['field'].'" >'.$value.'</textarea> &nbsp;  <span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                    break;
                case 'box' :
                    $setting_str = '$setting='.$v['setting'].';';
                    eval($setting_str);
                    $field_list[$k]['setting'] = $setting;
                    if ($setting['type'] == 'select') {
                        $str = '';
                        $str .= "<option value=''>--".L('PLEASE CHOOSE')."--</option>";
                        foreach ($setting['data'] as $v2) {
                            $str .= "<option value='$v2'";
                            $str .= $d_module[$v['field']] == $v2 ? ' selected="selected"':'';
                            $str .= ">$v2</option>";
                        }
                        $field_list[$k]['html'] = '<select id="'.$v['field'].'" name="'.$v['field'].'">'.$str.'</select> &nbsp;  <span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                        break;
                    } elseif ($setting['type'] == 'radio') {
                        $str = '';
                                $i = '';
                        foreach ($setting['data'] as $v2) {
                            $str .= " &nbsp; <input type='radio' name='".$v['field']."' id='".$v['field'].$i."' value='$v2'";
                            $str .= $d_module[$v['field']] == $v2 ? ' checked="checked"':'';
                            $str .= "/>&nbsp; $v2";
                            $i++;
                        }
                        $field_list[$k]['html'] = $str.'  <span id="'.$v['field'].'Tip" style="color:red;"></span>&nbsp; '.$input_tips;
                        break;
                    } elseif ($setting['type'] == 'checkbox') {
                        $str = '';
                        $i = '';
                        foreach ($setting['data'] as $v2) {
                            $str .= " &nbsp; <input type='checkbox' name='".$v['field']."[]' id='".$v['field'].$i."' value='$v2'";
                            if(strstr($d_module[$v['field']],$v2)){
                                $str .= ' checked="checked"';
                            }
                            $str .= '/>&nbsp;' .$v2;
                            $i++;
                        }
                        $field_list[$k]['html'] = $str.' <span id="'.$v['field'].'Tip" style="color:red;"></span>&nbsp; '.$input_tips;
                        break;
                    }
                    break;
                case 'editor' :
                    $upload_url = U('file/editor');
					$fileManagerJson = U('file/manager');
                    $field_list[$k]['html'] = '<script type="text/javascript">
                    var editor;
                    KindEditor.ready(function(K) {
                        editor = K.create(\'textarea[name="'.$v['field'].'"]\', {
                            uploadJson:"'.$upload_url.'",
                            allowFileManager : true,
                            loadStyleMode : false,
							fileManagerJson: "'.$fileManagerJson.'"
                        });
                    });
                    </script>
                    <textarea name="'.$v['field'].'" id="'.$v['field'].'" style="width: 800px; height: 350px;">'.$value.'</textarea> &nbsp;  <span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                    break;
                case 'datetime' :
					if($v['field'] == 'nextstep_time'){
						$time_accuracy = 'yyyy-MM-dd HH:mm';
					}else{
						$time_accuracy = 'yyyy-MM-dd';
					}
                    $field_list[$k]['html'] = '<input  onClick="WdatePicker({dateFmt:\''.$time_accuracy.'\'})" name="'.$v['field'].'" class="span2 Wdate" id="'.$v['field'].'" type="text" value="'.pregtime($value).'"/> &nbsp;  <span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                    break;
                case 'number' :
                    $field_list[$k]['html'] = '<input type="text"  id="'.$v['field'].'" name="'.$v['field'].'" maxlength="'.$v['maxlength'].'" value="'.$value.'"/> &nbsp;  <span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                    break;
                case 'floatnumber' :
                    $value = $value > 0 ? $value : ''; 
                    $field_list[$k]['html'] = '<input type="text" id="'.$v['field'].'" name="'.$v['field'].'" value="'.$value.'"/> &nbsp;  <span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                    break;
                case 'address':
                    if('add' == $type){
                        $defaultinfo = unserialize(M('Config')->where('name = "defaultinfo"')->getField('value'));
                        $state = $defaultinfo['state'];
                        $city = $defaultinfo['city'];
                    }else{
                        $address_array = explode(chr(10),$value);
                        $state = $address_array[0];
                        $city = $address_array[1];
                        $street = $address_array[2];
                    }
                    $field_list[$k]['html'] = '<script type="text/javascript">
                    $(function(){
                        new PCAS("'.$v['field'].'[\'state\']","'.$v['field'].'[\'city\']","'.$state.'","'.$city.'");
                    });
                    </script><select name="'.$v['field'].'[\'state\']" class="input-medium"></select> 
                        <select name="'.$v['field'].'[\'city\']" class="input-medium"></select>
                        <input type="text" name="'.$v['field'].'[\'street\']" placeholder="'.L('THE STREET INFORMATION').'" class="input-large" value="'.$street.'">';
                    break;
                case 'p_box':
                        $str = '';
                        $category = M('product_category');
                        $category_list = $category->select();
                        $categoryList = getSubCategory(0, $category_list, '');
                        foreach ($categoryList as $v2) {
                            $checked = '';
                            if($v2['category_id'] == $value){
                                $checked = 'selected="selected"';
                            }
                            $str .= "<option $checked value=".$v2['category_id'].">".$v2['name']."</option>";
                            
                        }
                        $field_list[$k]['html'] = '<select id="'.$v['field'].'" name="'.$v['field'].'">'.$str.'</select> &nbsp;  <span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;

                    break;
                case 'b_box':
                        $status = M('BusinessStatus')->order('order_id')->select();
                        $str = '';
                        foreach ($status as $v2) {
							$checked = '';
                            if($v2['status_id'] == $value){
                                $checked = 'selected="selected"';
                            }
                            $str .= "<option $checked value='".$v2['status_id']."'>".$v2['name']."</option>";
                        }
                        $field_list[$k]['html'] = '<select id="'.$v['field'].'" name="'.$v['field'].'">'.$str.'</select> &nbsp;  <span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                    break;
                default: 
                    if ($v['field'] == 'create_time' || $v['field'] == 'update_time') {
                        break;
                    }else{
                        $customer_id = intval($_GET['customer_id']);
                        if($v['field'] == 'name' && $customer_id) $value=M('customer')->where('customer_id = %d', $customer_id)->getField('name');
                        $field_list[$k]['html'] = '<input type="text" id="'.$v['field'].'" name="'.$v['field'].'" maxlength="'.$v['maxlength'].'" value="'.$value.'"/> &nbsp;  <span id="'.$v['field'].'Tip" style="color:red;"></span>'.$input_tips;
                    }
                    break;
            }
        }
        if($field_list[$k]['is_main'] == 1){
            $fieldlist['main'][] = $field_list[$k];
        }else{
            $fieldlist['data'][] = $field_list[$k];
        }
	}
	return $fieldlist;
}

/*
	返回码说明 短信函数返回1发送成功  0进入审核阶段 -4手机号码不正确
*/
//单条短信
//发送到目标手机号码 $telphone手机号码 $message短信内容
function sendSMS($telphone, $message, $sign_name="sign_name",$sendtime=''){
	$flag = 0; 
	$sms = F('sms');
	$argv = array( 
		'sn'=>$sms['uid'],
		'pwd'=>strtoupper(md5($sms['uid'].$sms['passwd'])),
		'mobile'=>$telphone,
		'content'=>urlencode($message.'【'.$sms[$sign_name].'】'),
		'ext'=>'',
		'rrid'=>'',
		'stime'=>$sendtime
	); 
	foreach ($argv as $key=>$value) { 
		if ($flag!=0) { 
			$params .= "&"; 
			$flag = 1; 
		} 
		$params.= $key."="; $params.= urlencode($value); 
		$flag = 1; 
    } 
	$length = strlen($params); 
	$fp = fsockopen("sdk2.entinfo.cn",8060,$errno,$errstr,10) or exit($errstr."--->".$errno); 
	$header = "POST /webservice.asmx/mdSmsSend_u HTTP/1.1\r\n"; 
	$header .= "Host:sdk2.entinfo.cn\r\n"; 
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
	$header .= "Content-Length: ".$length."\r\n"; 
	$header .= "Connection: Close\r\n\r\n"; 
	$header .= $params."\r\n"; 
	fputs($fp,$header); 
	$inheader = 1; 
	while (!feof($fp)) { 
		$line = fgets($fp,1024);
		if ($inheader && ($line == "\n" || $line == "\r\n")) { 
			$inheader = 0; 
		} 
		if ($inheader == 0) { 
		} 
	} 


	preg_match('/<string xmlns=\"http:\/\/tempuri.org\/\">(.*)<\/string>/',$line,$str);
	$result=explode("-",$str[1]);


	   
	if(count($result)>1){
		//echo '发送失败返回值为:'.$line."请查看webservice返回值";
		return $line;
	}else{
		//echo '发送成功 返回值为:'.$line;  
		return 1;
	}
}
function sendtestSMS($uid, $uname, $telphone){
	$flag = 0; 
	$sms = F('sms');
	$argv = array( 
		'sn'=>$uid,
		'pwd'=>strtoupper(md5($uid.$uname)),
		'mobile'=>$telphone,
		'content'=>urlencode('TEST SMS 【5KCRM】'),
		'ext'=>'',
		'rrid'=>'',
		'stime'=>$sendtime
	); 
	foreach ($argv as $key=>$value) { 
		if ($flag!=0) { 
			$params .= "&"; 
			$flag = 1; 
		} 
		$params.= $key."="; $params.= urlencode($value); 
		$flag = 1; 
    } 
	$length = strlen($params); 
	$fp = fsockopen("sdk2.entinfo.cn",8060,$errno,$errstr,10) or exit($errstr."--->".$errno); 
	$header = "POST /webservice.asmx/mdSmsSend_u HTTP/1.1\r\n"; 
	$header .= "Host:sdk2.entinfo.cn\r\n"; 
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
	$header .= "Content-Length: ".$length."\r\n"; 
	$header .= "Connection: Close\r\n\r\n"; 
	$header .= $params."\r\n"; 
	fputs($fp,$header); 
	$inheader = 1; 
	while (!feof($fp)) { 
		$line = fgets($fp,1024);
		if ($inheader && ($line == "\n" || $line == "\r\n")) { 
			$inheader = 0; 
		} 
		if ($inheader == 0) { 
		} 
	} 
	preg_match('/<string xmlns=\"http:\/\/tempuri.org\/\">(.*)<\/string>/',$line,$str);
	$result=explode("-",$str[1]);
	if(count($result)>1){
		//echo '发送失败返回值为:'.$line."请查看webservice返回值";
		return $line;
	}else{
		//echo '发送成功 返回值为:'.$line;  
		return 1;
	}
}

//多条短信 最多600条
//发送到目标手机号码字符串 用","隔开 $telphone手机号码 $message短信内容 
function sendGroupSMS($telphone, $message, $sign_name="sign_name",$sendtime=''){
	$flag = 0; 
	$sms = F('sms');
    //要post的数据 
	$argv = array( 
		'sn'=>$sms['uid'], ////替换成您自己的序列号
		'pwd'=>strtoupper(md5($sms['uid'].$sms['passwd'])), //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
		'mobile'=>$telphone,//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
		'content'=>urlencode($message.'【'.$sms[$sign_name].'】'),//短信内容
		'ext'=>'',
		'rrid'=>'',//默认空 如果空返回系统生成的标识串 如果传值保证值唯一 成功则返回传入的值
		'stime'=>$sendtime//定时时间 格式为2011-6-29 11:09:21
	); 
	//构造要post的字符串 
	foreach ($argv as $key=>$value) { 
		if ($flag!=0) { 
			$params .= "&"; 
			$flag = 1; 
		} 
		$params.= $key."="; $params.= urlencode($value); 
		$flag = 1; 
    } 
	$length = strlen($params); 
		 //创建socket连接 
	$fp = fsockopen("sdk2.entinfo.cn",8060,$errno,$errstr,10) or exit($errstr."--->".$errno); 
	//构造post请求的头 
	$header = "POST /webservice.asmx/mdSmsSend_u HTTP/1.1\r\n"; 
	$header .= "Host:sdk2.entinfo.cn\r\n"; 
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
	$header .= "Content-Length: ".$length."\r\n"; 
	$header .= "Connection: Close\r\n\r\n"; 
	//添加post的字符串 
	$header .= $params."\r\n"; 
	//发送post的数据 
	fputs($fp,$header); 
	$inheader = 1; 
	while (!feof($fp)) { 
		$line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据 
		if ($inheader && ($line == "\n" || $line == "\r\n")) { 
			$inheader = 0; 
		} 
		if ($inheader == 0) { 
			// echo $line; 
		} 
	} 


	preg_match('/<string xmlns=\"http:\/\/tempuri.org\/\">(.*)<\/string>/',$line,$str);
	$result=explode("-",$str[1]);


	   
	if(count($result)>1){
		//echo '发送失败返回值为:'.$line."请查看webservice返回值";
		return $line;
	}else{
		//echo '发送成功 返回值为:'.$line;  
		return 1;
	}
}
 function getSmsNum(){
	$sms = F('sms');
	
	$flag = 0; 
        //要post的数据 
	$argv = array( 
		'sn'=>$sms['uid'], //替换成您自己的序列号
		'pwd'=>$sms['passwd'],//替换成您自己的密码	
	); 
	//构造要post的字符串 
	foreach ($argv as $key=>$value) { 
		if ($flag!=0) { 
				 $params .= "&"; 
				 $flag = 1; 
		} 
		$params.= $key."="; $params.= urlencode($value); 
		$flag = 1; 
	} 
		$length = strlen($params); 
		 //创建socket连接 
		$fp = fsockopen("sdk2.entinfo.cn",8060,$errno,$errstr,10) or exit($errstr."--->".$errno); 
		//构造post请求的头 
		$header = "POST /webservice.asmx/GetBalance HTTP/1.1\r\n"; 
		$header .= "Host:sdk2.entinfo.cn\r\n"; 
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
		$header .= "Content-Length: ".$length."\r\n"; 
		$header .= "Connection: Close\r\n\r\n"; 
		//添加post的字符串 
		$header .= $params."\r\n"; 
		//发送post的数据 
		fputs($fp,$header); 
		$inheader = 1; 
		while (!feof($fp)) { 
			$line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据 
				if ($inheader && ($line == "\n" || $line == "\r\n")) { 
				$inheader = 0; 
			} 
			if ($inheader == 0) { 
				// echo $line; 
			} 
		} 
		//<string xmlns="http://tempuri.org/">-5</string>
		$line=str_replace("<string xmlns=\"http://tempuri.org/\">","",$line);
		$line=str_replace("</string>","",$line);
		$result=explode("-",$line);
		if(count($result)>1)
			return $line;
		else
			return $line;
}
//判断目录是否可写
function check_dir_iswritable($dir_path){
    $dir_path=str_replace('\\','/',$dir_path);
    $is_writale=1;
    if(!is_dir($dir_path)){
        $is_writale=0;
        return $is_writale;
    }else{
        $file_hd=@fopen($dir_path.'/test.txt','w');
        if(!$file_hd){
            @fclose($file_hd);
            @unlink($dir_path.'/test.txt');
            $is_writale=0;
            return $is_writale;
        }
        @unlink($dir_path.'/test.txt');
        $dir_hd=opendir($dir_path);
        while(false!==($file=readdir($dir_hd))){
            if ($file != "." && $file != "..") {
                if(is_file($dir_path.'/'.$file)){
                    //文件不可写，直接返回
                    if(!is_writable($dir_path.'/'.$file)){
                        return 0;
                    } 
                }else{
                    $file_hd2=@fopen($dir_path.'/'.$file.'/test.txt','w');
                    if(!$file_hd2){
                        @fclose($file_hd2);
                        @unlink($dir_path.'/'.$file.'/test.txt');
                        $is_writale=0;
                        return $is_writale;
                    }
                    @unlink($dir_path.'/test.txt');
                    //递归
                    $is_writale=check_dir_iswritable($dir_path.'/'.$file);
                }
            }
        }
    }
return $is_writale;
}

function is_email($email)
{
	return strlen($email) > 8 && preg_match("/^[-_+.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+([a-z]{2,4})|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email);
}
function is_phone($phone)
{
	return strlen(trim($phone)) == 11 && preg_match("/^1[3|5|8][0-9]{9}$/i", trim($phone));
}
function pregtime($timestamp){
	if($timestamp){
		return date('Y-m-d',$timestamp);
	}else{
		return '';
	}
}


function getContactsRQ($contacts_id,$width=200,$height=200){
	$contacts = M('Contacts')->where('contacts_id = %d', $contacts_id)->find();
	$customer_id = M('RContactsCustomer')->where('contacts_id = %d',$contacts_id)->getField('customer_id');
	$contacts['customer'] = M('Customer')->where('customer_id = %d', $customer_id)->getField('name');
	$qrOpt = array();
	$qrOpt['chl'] = "BEGIN:VCARD\nVERSION:3.0\n";
	$qrOpt['chl'] .= $contacts['name'] ? ("FN:".$contacts['name']."\n") : "";
	$qrOpt['chl'] .= $contacts['telephone'] ? ("TEL:".$contacts['telephone']."\n") : "";
	$qrOpt['chl'] .= $contacts['email'] ? ("EMAIL:".$contacts['email']."\n") : "";
	$qrOpt['chl'] .= $contacts['customer'] ? ("ORG:".$contacts['customer']."\n") : "";	
	$qrOpt['chl'] .= $contacts['post'] ? ("TITLE:".$contacts['post']."\n") : "";
	$qrOpt['chl'] .= $contacts['address'] ? ("ADR:".$contacts['address']."\n") : "";
	$qrOpt['chl'] .= "END:VCARD";
	
	$qrOpt['chs'] = $width."x".$height;
	$qrOpt['cht'] = "qr";
	$qrOpt['chld'] = "|1";
	$qrOpt['choe'] = "UTF-8";
	$link = 'http://chart.googleapis.com/chart?'.http_build_query($qrOpt);
	return $link;
}
function userLog($uid,$text=''){
    $user = M('user')->where(array('user_id'=>$uid))->find();
    $category = $user['category_id'] == 1 ? L('ADMIN') : L('USER');
    $data['user_id'] = $uid;
	$data['module_name'] = strtolower(MODULE_NAME);
    $data['action_name'] = strtolower(ACTION_NAME);
    $data['create_time'] = time();
 //   $data['action_id'] = $id;
    $data['content'] = sprintf('%s%s在%s%s。%s',$category,$user['name'],date('Y-m-d H:i:s'),L(ACTION_NAME),$text);
    $userLog = M('userLog');
    $userLog->create($data);
    if($userLog->add()){return true;}else{return false;}
    
}
function vali_permission($m, $a){
	$allow = $params['allow'];
	
	if (session('?admin')) {
		return true;
	}
	if (in_array($a, $allow)) {
		return true;
	} else {
		switch ($a) {
			case "listdialog" : $a = 'index'; break;
			case "adddialog" : $a = 'add'; break;
			case "excelimport" : $a = 'add'; break;
			case "excelexport" : $a = 'view'; break;
			case "cares" :  $a = 'index'; break;
			case "caresview" :  $a = 'view'; break;
			case "caresedit" :  $a = 'edit'; break;
			case "caresdelete" :   $a = 'delete'; break;
			case "caresadd" :  $a = 'add'; break;
			case "receive" : $a = 'add'; break;
			case "role_add" : $a = 'add';break;
			case "sendsms" : $a = 'marketing';break;
			case "sendemail" : $a = 'marketing';break;
		}
		$url = strtolower($m).'/'.strtolower($a);
		$ask_per = M('permission')->where('url = "%s" and position_id = %d', $url, session('position_id'))->find();
		if (is_array($ask_per) && !empty($ask_per)) {
			return true;
		} else {
			return false;
		}
	}
}
/**
 * @ atuhor		: Myron
 * @ function 	: 格式化print_r()，以方便调试
 **/
function println($data, $offset=true){
	if(empty($data)){
		echo '<pre>返回数据为空！</pre>';
	}else{
		echo '<pre>'; print_r($data); echo '</pre>';
	}
	if($offset){
		die;
	}
}

/**
 * @ atuhor		: xbl
 * @ function 	: 验证某条数据的权限
 **/
function check_permission($module_id, $module, $permission_field='owner_role_id'){
	$role_id = intval(session('role_id'));
	$owner_role_id = M($module)->where($module.'_id = %d', $module_id)->getField($permission_field);
	$permission_ids = getSubRoleId();
	if(in_array($owner_role_id, $permission_ids) || !$owner_role_id) return true;
	else return false;
}

/**
 * @ atuhor		: zf
 * @ function 	: 下载方法
 **/
 function download($file,$name=''){
    $fileName = $name ? $name : pathinfo($file,PATHINFO_FILENAME);
    $filePath = realpath($file);
    
    $fp = fopen($filePath,'rb');
    
    if(!$filePath || !$fp){
        header('HTTP/1.1 404 Not Found');
        echo "Error: 404 Not Found.(server file path error)<!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding -->";
        exit;
    }
    
    $fileName = $fileName .'.'. pathinfo($filePath,PATHINFO_EXTENSION);
    $encoded_filename = urlencode($fileName);
    $encoded_filename = str_replace("+", "%20", $encoded_filename);
    
    header('HTTP/1.1 200 OK');
    header( "Pragma: public" );
    header( "Expires: 0" );
    header("Content-type: application/octet-stream");
    header("Content-Length: ".filesize($filePath));
    header("Accept-Ranges: bytes");
    header("Accept-Length: ".filesize($filePath));
    
    $ua = $_SERVER["HTTP_USER_AGENT"];
    if (preg_match("/MSIE/", $ua)) {
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } else if (preg_match("/Firefox/", $ua)) {
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $fileName . '"');
    } else {
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
    }
    
    // ob_end_clean(); <--有些情况可能需要调用此函数
    // 输出文件内容
    fpassthru($fp);
    exit;
 }
 
 /**
  * @author		: myron
  * @function	: 获取表信息
  * @table_name	: 表名
  **/
function getTableInfo($table_name){
	$sql = 'SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = "'.C('DB_NAME').'" and table_name LIKE "'.C('DB_PREFIX').$table_name.'"';
	$result = M('')->query($sql);
	return $result;
}

function getSubCategoryTreeCode($category_id, $first=0) {
	$string = "";
	$department_list = M('ProductCategory')->where('parent_id = %d', $category_id)->select();

	if ($department_list) {
		if ($first) {
			$string = '<ul id="browser" class="filetree"><li style="list-style-type: none;" class="collapsable"><span rel="0" class="folder ta">全部 &nbsp; <span class="" id="0"> </span></span></li>';
		} else {
			$string = "<ul>";
		}
		foreach($department_list AS $value) {
			if($first){
				$string .= "<li style='list-style-type: none;'><span rel='".$value['category_id']."' class='folder ta'>".$value['name']." &nbsp; <span class='' id='".$value['category_id']."'> </span></span>".getSubCategoryTreeCode($value['category_id'])."</li>";
			} else {
				$string .= "<li style='list-style-type: none;'><span rel='".$value['category_id']."' class='file ta'>".$value['name']." &nbsp; <span class='' id='".$value['category_id']."'> </span></span>".getSubCategoryTreeCode($value['category_id'])."</li>";
			}
			
		}
		$string .= "</ul>";
	} 

	return $string;
}

/**
 * author : myrom
 * function : 截取字符长度，如果超过字符长度，后面追加...
 * @str : 要截取的字符串  $len : 要截取的长度
 **/
function cutString($str='', $len='15'){
	if(empty($str) || empty($len)) return false;
	$pre_content = strip_tags($str);
	$pre_content_len = mb_strlen($pre_content,'utf-8');
	if($pre_content_len <= $len){
		return $pre_content;
	}else{
		$pre_content = mb_substr($pre_content,0,$len,'utf-8');
		return $pre_content.' . . .';
	}
}

/**
 * author : myron
 * function : 在AuthenticateBehavior中判断是否AJAX请求，如果是AJAx请求且在弹窗页没有权限，直接显示无权限
 **/ 
function isAjaxRequest() {
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
		if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])){
			return true;
		}	
	}
	if(!empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])){
		// 判断Ajax方式提交
		return true;
	}
	return false;
}