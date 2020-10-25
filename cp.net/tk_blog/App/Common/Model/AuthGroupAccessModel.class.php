<?php
namespace Common\Model;
use Think\Model;
/**
 * 权限规则model
 */
class AuthGroupAccessModel extends Model{

	public function sendAdd(){
		$group_id = (int) I('post.group_id');
		$uname = trim(I('post.uname'));
		$nickname = trim(I('post.nickname'));
		$password = trim(I('post.password'));
		$repassword = trim(I('post.repassword'));
		$is_black = I('post.is_black') ? '0' : '1';
		$user_uid = trim(I('post.uid'));
		$email = trim(I('post.u_email'));
		$model = M('Users');
		$where = array('uid'=>$user_uid);
		if (!$group_id) {
			$this->error = '请选择管理组.^_^';
			return false;
		}
		if (!$user_uid) {
			if (!$uname || !$nickname) {
				$this->error = '用户名或昵称不能为空.^_^';
				return false;
			}
			if (!$email) {
				$this->error = '邮箱不能为空.^_^';
				return false;
			}
			if (!isEmail($email)) {
				$this->error = '邮箱格式不正确.^_^';
				return false;
			}
			if ($uname || $nickname || $email) {
				if ($model->where(array('uname'=>$uname))->find()) {
					$this->error = '该用户名已被使用.^_^';
					return false;
				}
				if ($model->where(array('nickname'=>$nickname))->find()) {
					$this->error = '该昵称已被使用.^_^';
					return false;
				}
				if ($model->where(array('u_email'=>$email))->find()) {
					$this->error = '该邮箱已被使用.^_^';
					return false;
				}
			}
			if (!$password || !$repassword) {
				$this->error = '密码或确认密码不能为空.^_^';
				return false;
			}
			if ($password != $repassword) {
				$this->error = '二次密码输入不一致.^_^';
				return false;
			}
		} else {
			$oldData = $model->where($where)->find();
			if ($oldData['uname'] != $uname) {
				if ($model->where(array('uname'=>$uname))->find()) {
					$this->error = '该用户名已被使用.^_^';
					return false;
				}
			}
			if ($oldData['nickname'] != $nickname) {
				if ($model->where(array('nickname'=>$nickname))->find()) {
					$this->error = '该昵称已被使用.^_^';
					return false;
				}
			}
			if ($oldData['u_email'] != $email) {
				if (!isEmail($email)) {
					$this->error = '邮箱格式不正确.^_^';
					return false;
				}
				if ($model->where(array('u_email'=>$email))->find()) {
					$this->error = '该邮箱已被使用.^_^';
					return false;
				}
			}
		}

		$data = array(
			'uname' => $uname,
			'password' => encrypt_password($password),
			'nickname' => $nickname,
			'add_time' => time(),
			'user_type' => 1,
			'is_black' => $is_black,
			'u_email' => $email
		);
		if ($user_uid) {
			$saveData = array(
				'uname' => $uname,
				'nickname' => $nickname,
				'is_black' => $is_black,
				'u_email' => $email
			);
			$model->where($where)->save($saveData);
			$this->where($where)->delete();
			$this->add(array('uid'=>$user_uid,'group_id'=>$group_id));
		} else {
			$uid = $model->add($data);
			$this->add(array('uid'=>$uid,'group_id'=>$group_id));
		}
		return true;
	}


	/**
	 * 根据group_id获取全部用户id
	 * @param  int $group_id 用户组id
	 * @return array         用户数组
	 */
	public function getUidsByGroupId($group_id){
		$user_ids=$this
			->where(array('group_id'=>$group_id))
			->getField('uid',true);
		return $user_ids;
	}

	/**
	 * 获取管理员权限列表 目前是单个管理 没有弄多个
	 */
	public function getListData($limit=5,$setConfig = array()){
		$count = M('Users')->where(array('user_type'=>1))->count();
		$Page = new \Think\Page($count,$limit);
		//设置分页显示
		if (!$setConfig) {
			$Page->setConfig('prev','Prev');
			$Page->setConfig('next','Next');
		} else {
			$Page->setConfig('prev',$setConfig['prev']);
			$Page->setConfig('next',$setConfig['next']);
		}
		$show = $Page->show();
		$data=$this
			->field('u.uid,u.uname,u.u_email,aga.group_id,ag.title')
			->alias('aga')
			->join('__USERS__ u ON aga.uid=u.uid','RIGHT')
			->join('__AUTH_GROUP__ ag ON aga.group_id=ag.id','LEFT')
			->where(array('u.user_type'=>1))
			->limit($Page->firstRow.','.$Page->listRows)
			->select();
		$result = array(
			'list' => $data,
			'page' => $show,
			'count'=> $count
		);
		return $result;
	}

	/**
	 * 获取后台 用户角色
	 * @param $uid 用户ID
	 */
	public function getGroupName($uid) {
		return $this->alias('aga')->join('__AUTH_GROUP__ ag ON aga.group_id=ag.id','LEFT')->where(array('aga.uid'=>$uid))->getField('title');
	}

}
