<?php
/**
 * 公共基类
 */
namespace Common\Controller;
use Think\Controller;
class HomebaseController extends Controller {
	public function _initialize() {
		//分配消息
		$this->msg = S('usermsg' . $_SESSION['user']['uid']);
	}

	//空操作
	public function _empty() {
		$this->error('该页面不存在！');
	}

	/**
	 * 检查用户登录
	 */
	protected function check_login(){
		if(!isset($_SESSION["user"])){
			$this->error('您还没有登录！',__ROOT__."/");
		}
	}

	/**
	 * 发送密码邮件
	 * @param $pwd  需要发送的密码 没有加密过的密码
	 * @return string
	 */
	protected  function sendEmaill_to_User($pwd){
		$option = M('Options')->where(array('option_name'=>'邮箱模板'))->find();
		if(!$option){
			$this->error('网站未配置账号激活信息，请联系网站管理员');
		}
		$options = json_decode($option['option_value'], true);
		//邮件标题
		$title = $options['title'];
		$username=$_SESSION['user']['uname'];

		//邮件内容
		$template = $options['content'];
		//激活地址
		$url = "www.mgchen.com";
		$content = str_replace(array('#username#','#password#','#link#'), array($username,$pwd,$url),$template);
		$send_result=send_email($_SESSION['user']['u_email'], $title, $content);
		if($send_result['error']){
			return $result = '邮件发送失败.ㄒoㄒ~';
		}
	}

	
}