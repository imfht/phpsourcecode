<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
|	系统钩子处理 发送邮件
+---------------------------------------------------------------------------
 */
namespace addons\mail;
use app\common\controller\Addons;
use app\common\controller\Send;

class Mail extends Addons {
	public $mail;
	public function __construct(){
		parent::__construct();
		$this->mail = new send();
	}
	
	/**
	 * [login_success 登陆成功]
	 * @Author   Jerry
	 * @DateTime 2017-04-28T15:24:37+0800
	 * @Example  eg:
	 * @return   [type]                   [description]
	 */ 
	public function loginSuccess($userinfo){
			if(!$userinfo) return ;
			if(getset('login_success')!="Y") return ;
			$data = $userinfo;
			$data['subject'] = "登陆成功通知";
			$content = "  您于".date('Y年m月d日 H时i分')." 登陆 ".getset('site_name')." <br/>登陆IP:".fetch_ip();
			$data['content'] = str_replace('{sitename}', getset('site_name'), $content);
			$data['logo'] = get_file_path(getset('system_logo'));
			$re=$this->mail->mail($userinfo['email'],$data);
			systemLog($userinfo['uid'],$userinfo['user_name'],'登陆成功','loginSuccess');

	}
	public function AdminIndex(){
		// echo "sssssssssss";
	}
	/**
	 * [reg_success 注册成功]
	 * @Author   Jerry
	 * @DateTime 2017-04-28T15:25:20+0800
	 * @Example  eg:
	 * @return   [type]                   [description]
	 */
	public function regSuccess($userinfo){
		if(!$userinfo) return ;
		if(getset('reg_success')!="Y") return ;
			$data = $userinfo;
			$data['subject'] = "注册成功通知";
			$content = str_replace('{username}', $userinfo['user_name'], getset('welcome_message_pm'));
			$data['content'] = str_replace('{sitename}', getset('site_name'), $content);
			$data['logo'] = get_file_path(getset('system_logo'));
			$re=$this->mail->mail($userinfo['email'],$data);
			systemLog($userinfo['uid'],$userinfo['user_name'],'注册成功','regSuccess');

	}
	/**
	 * [pushQuestionSuccess 发布问题成功]
	 * @Author   Jerry
	 * @DateTime 2017-05-02T10:27:55+0800
	 * @Example  eg:
	 * @param    [type]                   $userinfo [description]
	 * @return   [type]                             [description]
	 */
	public function pushQuestionSuccess($params){

	}
	/**
	 * [answeQuestionSuccess 加答成功,发邮件给问题的发布者]
	 * @Author   Jerry
	 * @DateTime 2017-05-02T10:28:33+0800
	 * @Example  eg:
	 * @param    [type]                   $params [description]
	 * @return   [type]                           [description]
	 */
	public function answeQuestionSuccess($params){

	}

}