<?php
/*
 *
 * sysmanage.Email  邮箱配置   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */	

class Email extends Action {
	private $cacheDir = ''; //缓存目录
	public
	function __construct() {
		_instance( 'Action/sysmanage/Auth' );
	}

	//得到邮件系统配置参数
	public function email_show() {
		$sql = "select * from fly_config_email;";
		$list = $this->C( $this->cacheDir )->findAll( $sql );
		if ( is_array( $list ) ) {
			foreach ( $list as $key => $row ) {
				$assArr[ $row[ "name" ] ] = $row[ "value" ];
			}
		}
		return $assArr;
	}
	//邮件系统配置
	public function email_config() {
		if ( empty( $_POST ) ) {
			$config = $this->email_show();
			$smarty = $this->setSmarty();
			$smarty->assign( array( "one" => $config ) );
			$smarty->display( 'sysmanage/email_config.html' );
		} else {
			foreach($_POST as $key=>$v){
				$sql="INSERT INTO fly_config_email(name,value) VALUES('$key','$v') 
						ON DUPLICATE KEY UPDATE value='$v'";
				$this->C($this->cacheDir)->update($sql);
			}
			$this->location("操作成功","/sysmanage/Email/email_config/");
		}
	}

	//邮件发送窗口
	public function email_send() {
		if ( empty( $_POST ) ) {
			$email = $this->_REQUEST( "email" );
			$smarty = $this->setSmarty();
			$smarty->assign( array( "email" => $email ) );
			$smarty->display( 'sysmanage/email_send.html' );
		} else {
			$config = $this->email_show();
			$MailServer = $config[ "server" ]; //SMTP 服务器
			$MailPort = $config[ "port" ]; //SMTP服务器端口号 默认25
			$MailId = $config[ "account" ]; //服务器邮箱帐号
			$MailPw = $config[ "password" ]; //服务器邮箱密码
			/**
			 *客户端信息
			 */
			$Title = $this->_REQUEST( "title" ); //邮件标题
			$Content = $this->_REQUEST( "content" ); //邮件内容
			$email = $this->_REQUEST( "email" ); //接收者邮箱

			if ( $this->email_send_api( $email, $Title, $Content ) ) {
				$this->L( "Common" )->ajax_json_success( "发送成功" );
			} else {
				$this->L( "Common" )->ajax_json_success( "发送成功" );
			}
		}
	}

	//邮件发送接口
	function email_send_api( $email, $tagsArr ,$mb_id=null) {
		$config = $this->email_show();
		
		$MailServer = $config[ "server" ]; //SMTP 服务器
		$MailPort  = $config[ "port" ]; //SMTP服务器端口号 默认25
		$MailId 	= $config[ "account" ]; //服务器邮箱帐号
		$MailPw 	= $config[ "password" ]; //服务器邮箱密码

		//判断是模板还是直接发送
		if($mb_id){
			$sql="select * from fly_config_email_mb where id='$mb_id'";
			$one=$this->C($this->cacheDir)->findOne($sql);
			$title	=$one["name"];
			$content=$one["content"];
		}else{
			$title	=$config["name"];
			$content=$config["content"];
		}
		//发送内容，替换标签为实际字符串
		$content=$this->L("Common")->replace_tags($tagsArr,$content);
		
		//第三方发送库
		$smtp = $this->L( "EmailSmtp", array( $MailServer, $MailPort, true, $MailId, $MailPw ) );
		$smtp->debug = false;
		if ( $smtp->sendmail( $email, $MailId, $title, $content, "HTML" ) ) {
			$this->email_log_add($content,$email,$ipaddr=null);
			return true;
		} else {
			return false;
		}
	}

} //end class
?>