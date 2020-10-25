<?php
namespace App\Http\Controllers;

use App\Lib\Api\AdminApi;
use App\Lib\Api\Api;
use Illuminate\Support\Facades\Input;
use Session,View,Mail;
class LoginController extends Controller
{		
	public function __construct()
	{
		$this->admin_api = new AdminApi;
		$this->api = new Api;
	}
	
	public function getIndex()
	{

		// 
		// var_dump(\Session::all());exit();
		if (!empty($_COOKIE['sys_cookie'])) {	
			$password = '';
			$sys_cookie = unserialize($_COOKIE['sys_cookie']);
			
			$user = $this->admin_api->getSystemUser(['user_name' => $sys_cookie['sys_name']]);
			$user = $user['result'][0];
			
			!empty($user) && $password = $user['password'];				
			if ($password === $sys_cookie['password']) {
				$this->createSession($user['system_user_id'], $user['user_name'], $user['nick_name'], explode(',', $user['role_list']),$user['member_id']);
				return \Redirect::to('/');
				// return View::make('index.index');
			}			
		}	
		
		$sys_id = Session::get('sys_id', '');
		
		$result = $this->admin_api->getSystemUser(['system_user_id' => $sys_id]);
		$result = $result['result'][0];
		if (empty($sys_id) || empty($result)) {
			return View::make('login.index');
		} else {
			// return View::make('index.index');
			return \Redirect::to('/');
		}
	}
	
	public function postIndex()
	{
		$username = Input::get('username', '');
		$password = Input::get('password', '');
		$type = Input::get('type', '');
		$keep_login = Input::get('keep_login', '');
		
		if (empty($username)) {
			return View::make('errors.msg')->with('msg', '请填写用户名');
		}
		if (empty($password)) {
			return View::make('errors.msg')->with('msg', '请填写密码');
		}
		if (empty($type)) {
			return View::make('errors.msg')->with('msg', '请选择管理员角色');
		}
		$user = $this->admin_api->getSystemUser(['user_name' => $username]);
		isset($user['result']) && $user = $user['result'];
		isset($user[0]) && $user = $user[0];
		// $user = $user['result'][0];
		if (empty($user['user_name'])) {
			return View::make('errors.msg')->with('msg', '此账号不存在或者登录角色不正确!');
		}
		//如果此管理员被关闭则
		if ($user['enabled']==0) {
			return View::make('errors.msg')->with('msg', '该管理员已被关闭');
		}

		//如果此用被删除则
		if ($user['status']==0) {
			return View::make('errors.msg')->with('msg', '该账号已被管理员禁用');
		}

		if ($user['type']==2) {
			$partner = $this->api->getPartner(['phone'=>$user['phone'],'page'=>1,'size'=>1]);
			isset($partner['result']) && $partner = $partner['result'];
			isset($partner[0]) && $partner = $partner[0];
			if(empty($partner)){
				return View::make('errors.msg')->with('msg', '此经销商账号未在小程序内注册，不能登录');
			}
			\Session::put('partner_id',$partner['id']);
			\Session::put('partner_member_id',$partner['member_id']);
		}
		
		$encode_password = $this->encode_password($password);	
		if ($encode_password != $user['password']) {
			// return View::make('errors.msg')->with('msg', '密码不匹配');
		}
	
		$vcode = trim(Input::get('vcode', ''));
		if (strcasecmp($vcode,session('vcode')) !== 0) {
			return View::make('errors.msg')->with('msg', '验证码不正确');
		}
	
		if (!empty($keep_login)) {
			$cookie_user['sys_name'] = $user['user_name'];
			$cookie_user['password'] = $user['password'];
			setcookie("sys_cookie", serialize($cookie_user), time() + 3600*24*7);
		}
		// var_dump($user);exit();
		$result = $this->createSession($user['system_user_id'], $user['user_name'], $user['nick_name'],$user['system_role_id'], $user['member_id']);
		if ($result === false) {
			return View::make('errors.msg')->with('msg', '您没有权限,请联系管理员');
		} else {
			return redirect()->action('IndexController@index');
		}
	}
	
	private function createSession($id, $user_name, $nick_name, $role, $member_id=0)
	{
		$data = $this->admin_api->getSystemRole(['system_role_id' => $role]);
		isset($data['result']) && $data = $data['result'];
		isset($data[0]) && $data = $data[0];

		if (!empty($data)) {
			Session::put('sys_id', $id);
			Session::put('user_name', $user_name);
			Session::put('nick_name', $nick_name);
			Session::put('system_role_id', $data['system_role_id']);
			Session::put('role_name', $data['name']);
			Session::put('grade', $data['grade']);
			Session::put('member_id', $member_id);
			// var_dump(\Session::all());exit();
		} else {
			return false;
		}
	}
	
	public function getOut()
	{
		// var_dump(\Session::all());exit();
		if (!empty($_COOKIE['sys_cookie'])) {
			setcookie('sys_cookie', '', time() - 3600 * 24 * 7);
		}
		Session::forget('sys_id');
		Session::forget('user_name');
		Session::forget('nick_name');
		Session::forget('system_role_id');
		Session::forget('role_name');
		Session::forget('grade');
		Session::forget('brand_id');
		
		return View::make('login/index');
	}
	public function sendMail(){
		$id = Input::get('id','');
		if(empty($id)){
			$data = $this->admin_api->getSystemMail(['status'=>0,'page'=>1,'size'=>1]);	
		}else{
			$data = $this->admin_api->getSystemMail(['id'=>$id,'page'=>1,'size'=>1]);	

		}
		isset($data['result']) && $data = $data['result'];
		foreach($data as $mail){
			$this->admin_api->updateSystemMail(['id'=>$mail['id'],'status'=>2]);
		}
		foreach($data as $mail){
			if(empty($mail['title'])){
				$mail['title'] = '智慧水站邮件';
			}
			if(!empty($mail['attach'])){
				if(file_exists($mail['attach'])){
					$flag = Mail::raw($mail['content'],function($message) use($mail){
					    $to = $mail['recive_mail'];
					    $message->to($to)->subject($mail['title']);

					    $attachment = $mail['attach'];
					    $data = explode('/',$mail['attach']);
					    //在邮件中上传附件
					    $message->attach($attachment,['as'=>end($data)]);
					});
				}else{
					$this->admin_api->updateSystemMail(['id'=>$mail['id'],'status'=>-1,'msg'=>'附件地址不存在']);

					Mail::raw('当天的日志数据不存在', function ($message)  use($mail){
					    $to = $mail['recive_mail'];
					    $message ->to($to)->subject($mail['title']);
					});
				}

			}else{
				Mail::raw($mail['content'], function ($message)  use($mail){
				    $to = $mail['recive_mail'];
				    $message ->to($to)->subject($mail['title']);
				});
				$this->admin_api->updateSystemMail(['id'=>$mail['id'],'status'=>1]);
			}
		}
		return 'Send Success!!!';
	}
}