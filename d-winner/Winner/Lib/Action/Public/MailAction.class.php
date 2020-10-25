<?php
/*
 * @varsion		EasyWork系统 1.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
class MailAction extends Action {
	/**
		* 邮件发送主方法
		*@param $mode 区分不同的邮件发送模板
		*@param $id  传人数据id
		*@examlpe 
	*/
    public function index($mode,$id=NULL,$pid=NULL){
		$Public = A('Index','Public');
		
		//main
		$id = intval($id);
		$pid = intval($pid);
		$type = intval($type);
		$user = M('User_table');
		$comy = M('User_company_table');
		$part = M('User_part_table');
		$userid = $_SESSION['login']['se_id'];
		$mailpwd = $user->where('id='.$userid)->getField('MailPwd');
		$this->assign('id',$id);
		$this->assign('pid',$pid);
		$this->assign('type',$type);
		$this->assign('mailpwd',$mailpwd);
		$this->assign('userid',$userid);
		$this->assign('uniqid',uniqid());
		unset($comy,$cinfo);
		if($mode==1){
			$this->display();
		}
	}
	
	
	//选择框控制
	public function defInfo($act){
		$Public = A('Index','Public');
		
		//main
		if($act=='comy'){
			$comy = M('User_company_table');
			$cinfo = $comy->cache(true)->where("`status`=1 and `type`=0")->field('id,name')->select();
			$str = '';
			foreach($cinfo as $t){
				$str .= '<option value="100'.$t['id'].'">'.$t['name'].'</option>';
			}
			echo $str;
			$str = '';
			unset($comy,$cinfo);
		}elseif($act=='part'){
			$part = M('User_part_table');
			$pinfo = $part->cache(true)->where("`status`=1")->field('id,name')->select();
			foreach($pinfo as $t){
				$str .= '<option value="'.$t['id'].'">'.$t['name'].'</option>';
			}
			echo $str;
			$str = '';
			unset($part,$pinfo);
		}elseif($act=='user'){
			$uinfo = $this->getAllUser();
			foreach($uinfo as $t){
				if($t['type']!=1){
					$str .= '<option id="'.$t['part_id'].'" value="'.$t['id'].'" cid="100'.$t['company_id'].'" mail="'.$t['email'].'">'.$t['username'].'</option>';
				}
			}
			echo $str;
			$str = '';
			unset($uinfo);
		}
		unset($Public);
	}
	
	public function change($act,$mode=NULL){
		$id = I('id');
		$main_user = C('DB_PREFIX').'user_main_table';
		$user_table = C('DB_PREFIX').'user_table';
		if($act=='comy'){
			$htm = '';
			$htms = '';		
			$part = M('User_part_table');
			$user = M('User_table');
			$pinfo = $part->cache(true)->field('id,name')->where('_parentId='.$id.' and `status`=1')->order('_parentId,convert(name using gbk)')->select();
			$map[$user_table.'.status'] = array('eq',1);
			if($id==0){
				$map[$user_table.'.id'] = array('neq',1);
			}else{
				$map[$main_user.'.company_id'] = array('eq',substr($id,3,strlen($id)));
			}
			$uinfo = $user->field($user_table.'.id,'.$user_table.'.email,'.$user_table.'.username,'.$main_user.'.part_id,'.$main_user.'.company_id')->join(' join '.$main_user.' on '.$main_user.'.user_id='.$user_table.'.id')->where($map)->order('convert(username using gbk)')->select();
			unset($map);
			
			if($mode==1){
				foreach($uinfo as $t){
					$htms .= '<option id="'.$t['part_id'].'" value="'.$t['id'].'" cid="100'.$t['company_id'].'" mail="'.$t['email'].'">'.$t['username'].'</option>'."\r\n";
				}
				echo $htms;
			}else{
				foreach($pinfo as $t){
					$htm .= '<option  id="'.$id.'" value="'.$t['id'].'">'.$t['name'].'</option>'."\r\n";
				}
				echo $htm;
			}
			
			unset($part,$uinfo,$pinfo,$id,$htm,$htms);
		}elseif($act=='part'){
			$htm = '';		
			$user = M('User_table');
			$map[$user_table.'.status'] = array('eq',1);
			if($id==0){
				$map[$user_table.'.id'] = array('neq',1);
			}else{
				$map[$main_user.'.part_id'] = array('eq',$id);
			}
			$uinfo = $user->field($user_table.'.id,'.$user_table.'.email,'.$user_table.'.username,'.$main_user.'.part_id,'.$main_user.'.company_id')->join(' join '.$main_user.' on '.$main_user.'.user_id='.$user_table.'.id')->where($map)->order('convert(username using gbk)')->select();
			unset($map);
			
			foreach($uinfo as $t){
				$htm .= '<option id="'.$t['part_id'].'" value="'.$t['id'].'" cid="100'.$t['company_id'].'" mail="'.$t['email'].'">'.$t['username'].'</option>'."\r\n";
			}
			echo $htm;
			unset($user,$uinfo,$id,$htm);
		}
	}
	
	public function getAllUser(){
		$user = M('User_table');
		$comy_user = C('DB_PREFIX').'user_company_table';
		$main_user = C('DB_PREFIX').'user_main_table';
		$user_table = C('DB_PREFIX').'user_table';
		$map[$user_table.'.status'] = array('eq',1);
		$map[$user_table.'.id'] = array('neq',1);
		$info = $user->field($user_table.'.id,'.$user_table.'.email,'.$user_table.'.username,'.$main_user.'.part_id,'.$main_user.'.company_id,'.$comy_user.'.type')->join(' join '.$main_user.' on '.$main_user.'.user_id='.$user_table.'.id')->join('left join '.$comy_user.' on '.$comy_user.'.id='.$main_user.'.company_id')->where($map)->order('convert(username using gbk)')->select();
		//dump($info);
		return $info;
	}
	
	private function getUserStr($uid,$act=NULL){
		$user = M('User_table');
		if($act==1){
			$info = $user->field('id,username')->where('id in('.$uid.')')->select();
			return $info;
		}else{
			$info = $user->field('GROUP_CONCAT(username) as username')->where('id in('.$uid.')')->select();
			return $info[0]['username'];
		}
		
		unset($user,$info);
	}
	
	public function sendnow(){
		$Public = A('Index','Public');
		$Mailer = A('Mail','Public');
		
		//main
		$user = M('User_table');
		$data = $Public->MC();
		$title = strval(I('title'));
		$uid = I('touser');
		$title = $title;
		$name = $data['username'];
		$notes = $data['username'];
		$m_cfg = array(
			'server'=>$data['smtp'],
			'ssl'=>$data['ssl'],
			'port'=>$data['port'],
			'user'=>$data['email'],
			'pwd'=>$data['pwd'],
			'name'=>$data['username'],
		);
		$contents = I('content');
		$send = $Mailer->set($title,$contents,$data);
		foreach($uid as $t){
			$t = intval($t);
			$info = $user->field('username,email')->where('id='.$t)->find();
			$to = $info['email'];
			$name = $info['username'];
			$Mailer->mailObj->AddAddress($to, $name);
		}
		$send = $Mailer->mailObj->send();
		if($send==1){
			echo 1;
		}else{
			echo 2;
			$mail = $Mailer->mailObj->ErrorInfo;
		}
		$Mailer->mailObj->ClearAddresses();
	}
}