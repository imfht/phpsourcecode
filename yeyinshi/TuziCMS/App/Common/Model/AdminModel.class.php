<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Common\Model;
use Think\Model\RelationModel;
class AdminModel extends RelationModel{//继承relation
		/**
		 * 自动验证   by TuziCMS
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				//array('username','','用户已经存在',0,'unique',1), //成功验证是否存在用户名
				//array('repassword','password','确认密码不正确',0,'confirm'),
				array('admin_name','require','用户名必须填写'),
				array('admin_pass','require','密码必须填写'),
				array('admin_myname','require','真实姓名必须填写'),


				//array('verify','checkCode','验证码错误!',0,'callback',1),
				//array('username','/^\w{2,}$/','用户名必须6个字母以上',0,'regex',1), //成功验证
				//array('verify','checkun','用户名错误!',0,'callback',1),
				array('admin_email','checkemail','电子邮件检测失败',0,'callback',0),
		);
		
		/**
		 * 处理checkemail回调函数   by TuziCMS
		 */
		protected function checkemail(){
			$email=$_POST['admin_email'];
			if(!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)){
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * 处理callback回调函数     by TuziCMS
		 */
		protected function checkun(){
			$username=$_POST['username'];
			if(strlen($username)>=6){
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * 处理验证条件中callback函数checkCode  by TuziCMS
		 */
		protected function checkCode($verify){
			if(md5($verify)!=$_SESSION['verify']){
				return false;
			}else{
				return true;
			}
		}
			
		
		/**
		 * 自动完成密码md5加密和危险字符过滤处理    by TuziCMS
		 */
		protected $_auto = array (
				//array('password','md5',1,'function') , //对password字段在新增的时候使md5函数处理
				//array('id','htmlspecialchars',3,'function'),
				array('admin_name','htmlspecialchars',3,'function'),
				array('admin_pass','htmlspecialchars',3,'function'),
				array('admin_login','htmlspecialchars',3,'function'),
				array('admin_myname','htmlspecialchars',3,'function'),
				array('admin_email','htmlspecialchars',3,'function'),
				//array('admin_oldip','htmlspecialchars',3,'function'),
				//array('admin_ip','htmlspecialchars',3,'function'),
				//array('admin_rsdate','htmlspecialchars',3,'function'),
				//array('admin_olddate','htmlspecialchars',3,'function'),
				array('admin_ok','htmlspecialchars',3,'function'),
				//array('admin_date','htmlspecialchars',3,'function'),
				array('admin_type','htmlspecialchars',3,'function'),	
				
		);
		

	}
?>

