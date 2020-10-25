<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

class IndexAction extends Action {
	/**
		* 项目主入口
		*@examlpe 
	*/
    public function index(){
		$Public = A('Index','Public');
		$Public->check('Index',array('r'));
		session(NULL);
		
		//main
		$uid = $_SESSION['login']['se_id'];
		$gid = $_SESSION['login']['se_groupID'];
		$cid = $_SESSION['login']['se_comyID'];
		$pid = $_SESSION['login']['se_partID'];
		
		$type = $Public->GS('User_company_table',$cid,'type');
		$this->assign('type',$type);
		$this->assign('uid',$uid);
		$this->assign('gid',$gid);
		$this->assign('cid',$cid);
		$this->assign('pid',$pid);
		
		$menu = M('Menu');
   		$info = $menu->cache(true)->where('_parentId=0 and status=1')->order('sort,id')->select();
		//dump($info);exit;
		$group_access = $Public->GS('User_group_table',$gid);
		
		$logo = ROOT.'Skin/'.GROUP_NAME;
		
		$this->assign('group_access',$group_access);
		$this->assign('uniqid',uniqid());
		$this->assign('info',$info);
		$this->display();
		unset($info,$menu,$Public);
    }
	
	/**
		* 获取未读信息数
		*@examlpe 
	*/
	public function getsms(){
		$sms_receive = M('Sms_receive_table');
		$uid = $_SESSION['login']['se_id'];
		$count = $sms_receive->where('user_id='.$uid.' and `status`=0')->count();
		echo $count;
	}
	
	/**
		* 显示信息
		*@examlpe 
	*/
	public function showsms($act=0,$json=NULL){
		$sms_receive = M('Sms_receive_table');
		$uid = $_SESSION['login']['se_id'];
		if($act==0){
			$count = $sms_receive->where('user_id='.$uid.' and status=0')->count();
			echo $count;
		}elseif($act==1){
			if($json==1){
				$sms = D('Sms_table');
				$receive = $sms_receive->field('GROUP_CONCAT(sms_id ORDER BY sms_id) as sms_id')->where('`user_id`='.$uid)->find();
				$info = $sms->relation('user')->where('`id` in ('.$receive['sms_id'].')')->select();
				$item = array();
				foreach($info as $t){
					if($t['status']==0){
						$t['title'] = '<strong>'.$t['title'].'</strong>';
					}
					$item[] = $t;
				}
				echo $info = json_encode($item);
			}else{
				$this->display();
			}	
		}
	}
	
	/**
		* 操作信息表
		*@examlpe 
	*/
	public function smsact($act){
		$sms = M('Sms_table');
		$sms_receive = M('Sms_receive_table');
		$sms_base = M('Sms_baseinfo_table');
		$uid = $_SESSION['login']['se_id'];
		$sql = $sms_receive->field('sms_id as id')->where('`user_id`='.$uid)->select(false);
		if($act=='readed'){
			$data = array(
				'status'=>1
			);
			$edit = $sms->where('`id` in('.$sql.')')->save($data);
			$edit2 = $sms_receive->where('`user_id`='.$uid)->save($data);
			if($edit !== false && $edit2 !== false){
				echo 1;
			}else{
				echo 0;
			}
		}elseif($act=='clear'){
			$del = $sms->where('id in('.$sql.')')->delete();
			if($del){
				$del2 = $sms_base->where('`sms_id` in('.$sql.')')->delete();
				$del3 = $sms_receive->where('`user_id`='.$uid)->delete();
				echo 1;
			}else{
				echo 0;
			}
		}
	}
	
	/**
		* 信息详情
		*@examlpe 
	*/
	public function smsdetail($id){
		$id = intval($id);
		$sms = D('Sms_table');
		$sms_receive = M('Sms_receive_table');
		$map['id'] = array('eq',$id);
		$data = array(
			'status'=>1
		);
		$sms->where($map)->save($data);
		$sms_receive->where('`sms_id`='.$id)->save($data);
		$info = $sms->relation(true)->where($map)->find();
		unset($map);
		$this->assign('uniqid',uniqid());
		$this->assign('info',$info);
		$this->display();
	}
	
	/**
		* 头部区域
		*@examlpe 
	*/
	public function top(){		
		$this->display();
    }
	
	/**
		* 左侧菜单栏区域
		*@examlpe 
	*/
	public function left(){
		$this->display();
    }
	
	/**
		* 右侧系统信息页
		*@examlpe 
	*/
	public function main($json=NULL){
		$Public = A('Index','Public');
		$role = $Public->check('Main',array('r'));
		
		//main
		$nowtime = time();
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
		$sys->root = ITEM;
		$sys->charset = C('CFG_CHARSET');
		$App = A('App','Public');
		
		//main
		$notice = M('Notice_table');
		if($role=='all'){
			$isadmin = 1;
		}elseif($role['user_group'][0]['access']=='999'){
			$isadmin = 2;
		}else{
			$isadmin = 0;
		}
		$ninfo = $notice->where('status>0')->order('status asc,addtime desc')->select();
		$this->assign('app',$App);
		$nowdate = date("Y-m-d",$nowtime);
		$path = CONF_PATH.'version.txt';
		$ver = $sys->getFile($path);
		$ver = preg_replace("/;[\r\n]/iu",";\n",$ver);
		$arr_ver = explode(";\n",$ver);
		$arr_ver = array_filter($arr_ver);
		$config = M('config');
		$serial = $config->where("keyword='CFG_APPID'")->getField('vals');
		$this->assign('ninfo',$ninfo);
		$this->assign('serial',$serial);
		$this->assign('ver',$arr_ver);
		$this->assign('nowtime',$nowtime);
		$this->display();
		
		unset($Public,$role);
    }
	
	/**
		* 升级检查
		*@examlpe 
	*/
	public function upver(){
		$nowtime = time();
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
		$sys->root = ITEM;
		$sys->charset = C('CFG_CHARSET');
		$App = A('App','Public');
		
		//main
		$nowdate = date("Y-m-d H:i:s",$nowtime);
		$path = CONF_PATH.'version.txt';
		$ver = $sys->getFile($path);
		$arr_ver = explode(";\r\n",$ver);
		$arr_ver = array_filter($arr_ver);
		$content = $arr_ver[0].";\r\n"
		 .$nowdate.";\r\n"
		 .$arr_ver[2].";\r\n";
		$sys->putFile($path,$content);
		echo "当前版本：$arr_ver[0]&nbsp;&nbsp;&nbsp;&nbsp;最后检测时间：$nowdate";
	}
	
	/**
		* 下载升级包
		*@examlpe 
	*/
	public function downver(){
		load("@.download");
		
		//main
		$soft = I('soft');
		download($soft,$name);
	}
	
	/**
		* 系统登录方法
		*@examlpe 
	*/
	public function login(){
		//main
		if($this->isPost()){
			$users = D('User_table');	
			$user = check_sql(I('username'));
			$pwd = $this->_post('password','md5');
			$code = check_sql(I('code'));
			$fields = array(
				'username'=>$user,
				'password'=>$pwd,
				'_logic'=>'AND'
			);
			$info = $users->relation(true)->where($fields)->find();
			//dump($info);exit;
			unset($fields);
			if($user==''){
				 $this->error('用户名不能为空！');
			}
			if($this->_post('password')==''){
				 $this->error('密码不能为空！');
			}
			if($code=='' && C('CODE_ON')==1){
				 $this->error('验证码不能为空！');
			}
			
			if(C('CODE_ON')){
				if(md5($code)==session('verify')){
					$check_code = 1;
				}
			}else{
				$check_code = 1;
			}
			
			if($check_code){
				if(!count($info)>0){
					$this->error('账号或密码不正确！');
				}else{
					session(array('path'=>CONF_PATH.'/Session','prefix'=>'login'));
					session('se_user',$info['username']);
					session('se_id',$info['id']);
					session('se_group',$info['user_group'][0]['name']);
					session('se_groupID',$info['user_main']['group_id']);
					session('se_comyID',$info['user_main']['company_id']);
					session('se_partID',$info['user_main']['part_id']);
					//dump(session('se_user'));exit;
					$fields = array(
						'login_count'=>$info['login_count']+1,
						'last_visit'=>time(),
					);
					$up = $users->where("id=".$info['id'])->save($fields);
					unset($fields);
					header('Location:'.ITEM.'/index.php?s=/'.GROUP_NAME);
				}
			}else{
				$this->error('验证码不正确！');
			}
		}
		$this->display();
	}
	
	/**
		* 注销用户
		*@examlpe 
	*/
	public function safe(){
		session(array('prefix'=>'login'));
		session('[destroy]');
		redirect(ITEM.'/index.php?s=/'.GROUP_NAME.'/Index/login',0);
    }
	
	/**
		* 获取验证码
		*@examlpe 
	*/
	public function verify(){
    	import('ORG.Util.Image');
		$model = C('CODE_MODEL');
		$len = C('CODE_LEN');
    	Image::buildImageVerify($len,$model);
	}
	
	/**
		* 转换左侧菜单栏数据格式，并输出Json
		*@examlpe 
	*/
	public function json($mid){
		$Left = A('Left','Public');
		$Left->table = 'Menu';
		
		//main
		if(is_int((int)$mid)){
			$user = D('User_table');
			$uid = $_SESSION['login']['se_id'];
			$sele = $user->relation('user_group')->where('id='.$uid)->find();
			$Left->access = $sele['user_group'][0]['access'];
			$info = $Left->rowMenu($mid,$uid);
			//dump($info);
			echo json_encode($info);
			unset($info,$sele,$user,$Left);
		}	
	}
	
	/**
		* 清空所以搜索产生的cookies
		*@examlpe 
	*/
	public function clear($act=NULL){
		if($act=='view'){
			cookie('view',NULL);
		}else{
			cookie(NULL,'map');
		}
	}
	
	/**
		* 清空所以缓存数，并重新生成Json
		*@examlpe 
	*/
	public function cache(){
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
		
		//main
    	$temp_path = RUNTIME_PATH.'/';
		if(file_exists($temp_path)){
			$dt = $sys->delFile($temp_path);
			R(GROUP_NAME.'/User/json',array(NULL));
			R(GROUP_NAME.'/Comy/json',array(NULL));
			R(GROUP_NAME.'/Part/json',array(NULL));
			R(GROUP_NAME.'/Linkage/json',array(NULL));
			R(GROUP_NAME.'/Group/json',array(NULL));
			R(GROUP_NAME.'/Menu/json',array(NULL));
		}
		echo 1;
		unset($sys,$field_path,$temp_path);
	}
}