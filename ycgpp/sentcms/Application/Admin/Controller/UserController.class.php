<?php

// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Common\Api\UserApi;

class UserController extends \Common\Controller\AdminController
{
	
	/**
	 * 用户管理首页
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function index() {
		$nickname = I('nickname');
		$map['status'] = array('egt', 0);
		if (is_numeric($nickname)) {
			$map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
		} 
		else {
			$map['nickname'] = array('like', '%' . (string)$nickname . '%');
		}
		
		$list = $this->lists('Member', $map);
		int_to_string($list);
		$this->assign('_list', $list);
		$this->setMeta('用户信息');
		$this->display();
	}

	/**
	 * create
	 * @author colin <colin@tensent.cn>
	 */
	public function create(){
		if(IS_POST){
			$model = D('Member');
			$data = $model->create();
			if(!$data){
				$this->error($this->showRegError($model->getError()));
			}
			/* 检测密码 */
            if(I('post.password') != I('post.repassword')){
                $this->error('密码和重复密码不一致！');
            }
			/*调用注册接口*/
			$user = new UserApi;
			$uid = $user->register($data['username'], I('post.password'), $data['email']);

			if(0 < $uid){
				$userinfo = array('nickname' => $data['username'], 'status' => 1,'reg_time'=>time(),'last_login_time'=>time(),'last_login_ip'=>get_client_ip(1));
				/*保存信息*/
				if(!M('Member')->where(array('uid'=>$uid))->save($userinfo)){
					$this->error('用户添加失败！');
				} else {
					$this->success('用户添加成功！',U('index'));
				}
			}else{
				$this->error($this->showRegError($uid));
			}
		}else{
			$builder = new \OT\Builder('config');
			$builder->title('新增用户')
					->keyText('username','用户名','用户名会作为默认的昵称')
					->key('password','密码','用户密码不能少于6位','password')
					->key('repassword','确认密码','','password')
					->keyText('email','邮箱','用户邮箱，用于找回密码等安全操作')
					->buttonSubmit()->buttonBack()
					->display();
		}
	}
	
	/**
	 * 修改昵称初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function edit() {
		if(IS_POST){
			$model = D('Member');
			$data = $model->create();
			if(!$data){
				$this->error($this->showRegError($model->getError()));
			}
			//post    password
			$password = I('post.password');
			//为空
			if(empty($password)){
				unset($data['password']);
				unset($data['salt']);
				$model->save($data);
			}else{
				$data['salt'] = rand_string();
				$data['password'] = md5($password.$data['salt']);
				//不为空
				$model->save($data);
			}
			$this->success('修改成功！',U('index'));
		}else{
			$info = $this->getUserinfo();

			$field = array(
				array('name'=>'uid','type'=>'hidden'),
				array('name'=>'username','title'=>'用户名','type'=>'readonly'),
				array('name'=>'nickname','title'=>'昵称','type'=>'text'),
				array('name'=>'password','title'=>'密码','type'=>'password','help'=>'为空时则不修改'),
				array('name'=>'sex','title'=>'性别','type'=>'select','opt'=>array('0'=>'保密','1'=>'男','2'=>'女')),
				array('name'=>'email','title'=>'邮箱','type'=>'text','help'=>'用户邮箱，用于找回密码等安全操作'),
				array('name'=>'qq','title'=>'QQ','type'=>'text'),
				array('name'=>'score','title'=>'用户积分','type'=>'text'),
				array('name'=>'signature','title'=>'用户签名','type'=>'text'),
				array('name'=>'status','title'=>'状态','type'=>'select','opt'=>array('0'=>'禁用','1'=>'启用')),
			);
			$data = array(
				'info'  => $info,
				'keyList' => $field
			);
			$this->assign($data);
			$this->setMeta("编辑用户");
			$this->display('Public/edit');
		}
	}

	/**
	 * del
	 * @author colin <colin@tensent.cn>
	 */
	public function del(){
		$ids = I('post.ids');
		//多条删除和单条删除
		empty($ids) ? $ids = I('get.id') : $ids = $ids;
		$uid = array('IN',is_array($ids) ? implode(',',$ids) : $ids);
		//获取用户信息
		$find = $this->getUserinfo($uid);
		D('Member')->where(array('uid'=>$uid))->delete();
		$this->success('删除用户成功！');
	}


	public function auth(){
		$access = D('AuthGroupAccess');
		if (IS_POST) {
			$uid = I('uid','','trim,intval');
			$access->where(array('uid'=>$uid))->delete();
			$group_type = C('USER_GROUP_TYPE');
			foreach ($group_type as $key => $value) {
				$group_id = I($key,'','trim,intval');
				if ($group_id) {
					$add = array(
						'uid' => $uid,
						'group_id' => $group_id,
					);
					$access->add($add);
				}
			}
			$this->success("设置成功！");
		}else{
			$uid = I('id','','trim,intval');
			$row = D('AuthGroup')->select();
			$auth = $access->where(array('uid'=>$uid))->select();

			foreach ($auth as $key => $value) {
				$auth_list[] = $value['group_id'];
			}
			foreach ($row as $key => $value) {
				$list[$value['module']][] = $value;
			}
			$data = array(
				'uid'   => $uid,
				'auth_list' => $auth_list,
				'list' => $list
			);
			$this->assign($data);
			$this->setMeta("用户分组");
			$this->display();
		}
	}

	/**
	 * 获取某个用户的信息
	 * @var uid 针对状态和删除启用
	 * @var pass 是查询password
	 * @var errormasg 错误提示
	 * @author colin <colin@tensent.cn>
	 */
	private function getUserinfo($uid = null,$pass = null,$errormsg = null){
		$uid = $uid ? $uid : I('get.id');
		//如果无UID则修改当前用户
		$uid = $uid ? $uid : session('user_auth.uid');
		$map['uid'] = $uid;
		if($pass != null ){
			unset($map);
			$map['password'] = $pass;
		}
		$list = D('Member')->where($map)->field('uid,username,nickname,sex,email,qq,score,signature,status,salt')->find();
		if(!$list){
			$this->error($errormsg ? $errormsg : '不存在此用户！');
		}
		return $list;
	}
	
	/**
	 * 修改昵称提交
	 * @author huajie <banhuajie@163.com>
	 */
	public function submitNickname() {
		
		//获取参数
		$nickname = I('post.nickname');
		$password = I('post.password');
		empty($nickname) && $this->error('请输入昵称');
		empty($password) && $this->error('请输入密码');
		
		//密码验证
		$User = new UserApi();
		$uid = $User->login(UID, $password, 4);
		($uid == - 2) && $this->error('密码不正确');
		
		$Member = D('Member');
		$data = $Member->create(array('nickname' => $nickname));
		if (!$data) {
			$this->error($Member->getError());
		}
		
		$res = $Member->where(array('uid' => $uid))->save($data);
		
		if ($res) {
			$user = session('user_auth');
			$user['username'] = $data['nickname'];
			session('user_auth', $user);
			session('user_auth_sign', data_auth_sign($user));
			$this->success('修改昵称成功！');
		} 
		else {
			$this->error('修改昵称失败！');
		}
	}
	
	/**
	 * 修改密码初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function updatePassword() {
		$this->setMeta('修改密码');
		$this->display('updatepassword');
	}
	
	/**
	 * 修改密码提交
	 * @author huajie <banhuajie@163.com>
	 */
	public function submitPassword() {
		
		//获取参数
		$password = I('post.old');
		empty($password) && $this->error('请输入原密码');
		$data['password'] = I('post.password');
		empty($data['password']) && $this->error('请输入新密码');
		$repassword = I('post.repassword');
		empty($repassword) && $this->error('请输入确认密码');
		
		if ($data['password'] !== $repassword) {
			$this->error('您输入的新密码与确认密码不一致');
		}
		
		$Api = new UserApi();
		$res = $Api->updateInfo(UID, $password, $data);
		if ($res['status']) {
			$this->success('修改密码成功！');
		} 
		else {
			$this->error($res['info']);
		}
	}
	
	/**
	 * 用户行为列表
	 * @author huajie <banhuajie@163.com>
	 */
	public function action() {
		//获取列表数据
		$Action = M('Action')->where(array('status' => array('gt', -1)));
		$list = $this->lists($Action);
		int_to_string($list);
		
		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);
		
		$this->assign('_list', $list);
		$this->setMeta('用户行为');
		$this->display();
	}
	
	/**
	 * 新建用户行为
	 * @author colin <colin@tensent.cn>
	 */
	public function Addaction(){
		if(IS_POST){
			$model = D('Action');
			$data = $model->create();
			if(!$data){
				$this->error($model->getError());
			}
			$model->add();
			$this->success('添加成功！',U('action'));
		}else{
			$builder = new \OT\Builder('config');
			$type = get_action_type(null,true);
			$builder->title('新增行为')
					->keyText('name','行为标识','输入行为标识 英文字母')
					->keyText('title','行为名称','输入行为名称')
					->keySelect('type','行为类型','选择行为类型',$type)
					->keyText('remark','行为描述','输入行为描述')
					->keyTextarea('rule','行为规则','输入行为规则，不写则只记录日志')
					->keyTextarea('log','日志规则','记录日志备注时按此规则来生成，支持[变量|函数]。目前变量有：user,time,model,record,data')
					->data($find)
					->buttonSubmit()->buttonBack()
					->display();
		}
	}
	
	/**
	 * 编辑用户行为
	 * @author colin <colin@tensent.cn>
	 */
	public function editAction(){
		$find = $this->CheckData('Action','id');
		if(IS_POST){
			$model = D('Action');
			$data = $model->create();
			if(!$data){
				$this->error($model->getError());
			}
			$model->save();
			$this->success('修改成功！',U('action'));
		}else{
			$builder = new \OT\Builder('config');
			$type = get_action_type(null,true);
			$builder->title('编辑行为')
					->keyHidden('id')
					->keyReadonly('name','行为标识','输入行为标识 英文字母')
					->keyText('title','行为名称','输入行为名称')
					->keySelect('type','行为类型','选择行为类型',$type)
					->keyText('remark','行为描述','输入行为描述')
					->keyTextarea('rule','行为规则','输入行为规则，不写则只记录日志')
					->keyTextarea('log','日志规则','记录日志备注时按此规则来生成，支持[变量|函数]。目前变量有：user,time,model,record,data')
					->data($find)
					->buttonSubmit()->buttonBack()
					->display();
		}
	}

	/**
	 * 修改用户行为状态
	 * @author colin <colin@tensent.cn>
	 */
	public function Actionstatus(){
		$model = D('Action');
		$map['id'] = '';
		if(IS_POST){
			//修改多条
			$map['id'] = array('IN',implode(',',I('post.ids')));
			$data['status'] = I('get.status');
			$message = I('get.status') ? '启用' : '禁用';
			$model->where(array($map))->save($data);
		}else{
			//修改单条
			$find = $this->CheckData('Action','id');
			$map['id'] = I('get.id');
			$message = $find['status'] ? '禁用' : '启用';
			$data['status'] = $find['status'] ? 0 : 1;
			$model->where($map)->save($data);
		}
		$this->success('设置'.$message.'状态成功！');
	}
	
	/**
	 * 更新行为
	 * @author huajie <banhuajie@163.com>
	 */
	public function saveAction() {
		$res = D('Action')->update();
		if (!$res) {
			$this->error(D('Action')->getError());
		} 
		else {
			$this->success($res['id'] ? '更新成功！' : '新增成功！', Cookie('__forward__'));
		}
	}
	
	/**
	 * 删除用户行为状态
	 * @author colin <colin@tensent.cn>
	 */
	public function Actiondel(){
		$model = D('Action');
		$map['id'] = '';
		if(IS_POST){
			//删除多条
			$map['id'] = array('IN',implode(',',I('post.ids')));
			$model->where($map)->delete();
		}else{
			//删除单条
			$find = $this->CheckData('Action','id');
			$map['id'] = I('get.id');
			$model->where($map)->delete();
		}
		$this->success('删除成功！');
	}

	/**
	 * 会员状态修改
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function changeStatus($method = null) {
		$id = array_unique((array)I('id', 0));
		if (in_array(C('USER_ADMINISTRATOR'), $id)) {
			$this->error("不允许对超级管理员执行该操作!");
		}
		$id = is_array($id) ? implode(',', $id) : $id;
		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}
		$map['uid'] = array('in', $id);
		switch (strtolower($method)) {
			case 'forbiduser':
				$this->forbid('Member', $map);
				break;

			case 'resumeuser':
				$this->resume('Member', $map);
				break;

			case 'deleteuser':
				$this->delete('Member', $map);
				break;

			default:
				$this->error('参数非法');
		}
	}

	/**
	 * 确认某条数据是否存在
	 * @author colin <colin@tensent.cn>
	 */
	protected function CheckData($model = 'User' , $field = 'uid'){
		$map[$field] = I('get.id');
		$model = D($model);
		$find = $model->where($map)->find();
		if(!$find){
			$this->error('不存在此条数据！');
		}
		return $find;
	}
	
	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0) {
		switch ($code) {
			case -1:
				$error = '用户名长度必须在16个字符以内！';
				break;

			case -2:
				$error = '用户名被禁止注册！';
				break;

			case -3:
				$error = '用户名被占用！';
				break;

			case -4:
				$error = '密码长度必须在6-30个字符之间！';
				break;

			case -5:
				$error = '邮箱格式不正确！';
				break;

			case -6:
				$error = '邮箱长度必须在1-32个字符之间！';
				break;

			case -7:
				$error = '邮箱被禁止注册！';
				break;

			case -8:
				$error = '邮箱被占用！';
				break;

			case -9:
				$error = '手机格式不正确！';
				break;

			case -10:
				$error = '手机被禁止注册！';
				break;

			case -11:
				$error = '手机号被占用！';
				break;

			default:
				$error = '未知错误';
		}
		return $error;
	}
}
