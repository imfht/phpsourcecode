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
class ConfigModel extends RelationModel{//继承relation
		
		/**
		 * 自动验证   
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				array('config_webname','require','网站名称必须填写'),
				array('config_webtitle','require','站点标题必须填写!'), //成功验证用户名是否填写
				array('config_webkw','require','站点关键字必须填写!'),
				array('config_cp','require','站点描述必须填写!'),
				array('config_tel','require','电话必须填写!'),
				
				
				//array('username','','用户已经存在',0,'unique',1), //成功验证是否存在用户名
					
				//array('password','require','密码必须填写!'), //成功验证密码是否填写
				//array('repassword','password','确认密码不正确',0,'confirm'),
		
				
				array('content','require','留言内容必须填写'),
				//array('verify','checkCode','验证码错误!',0,'callback',1),
				//array('username','/^\w{2,}$/','用户名必须6个字母以上',0,'regex',1), //成功验证
				array('config_email','checkemail','客服邮箱检测失败',0,'callback',0),
				array('config_qq','checkqq','客服QQ检测失败',0,'callback',0),
				
				array('config_weburl','checkweburl','公司网址检测失败',0,'callback',3),
		);
		/**
		 * checkweburl函数 
		 */
		protected function checkweburl(){
			$content=$_POST['config_weburl'];
			if(preg_match("/^[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/", $content)){
				return true;
			}else{
				return false;
			}
		}
		
		/**
		 * 处理checkemail回调函数   
		 */
		protected function checkemail(){
			$email=$_POST['config_email'];
			if(!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)){
				return false;
			}else{
				return true;
			}
		}
		/**
		 * 处理checkqq回调函数   
		 */
		protected function checkqq(){
			$qq=$_POST['config_qq'];
			if(!preg_match("/^\d+$/i", $qq)){
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * 模型关联 数据库表的关联  
		 */
		protected $_link = array(
				'User'=>array( //dept可以随便取名字
						'mapping_type' => self::BELONGS_TO,//这里跟3.1有点不一样
						'class_name' => 'User', //要关联的模型类名(即表名)
		
						'mapping_name'=> 'id',//关联的映射名称，用于获取数据用(附表的关联字段)
						'foreign_key'=>'uid', //关联的外键Id(主表的关联字段)
		
						'mapping_fields'=>array('user_name'), //关联要查询的字段
						'as_fields'=>'user_name', //直接把关联的字段值映射成数据对象中的某个字段
				),
		);
		
		
		/**
		 * 自动完成，在create时自动执行
		 * array('填充字段','填充内容','填充条件','附加规则');
		 */
		protected $_auto=array(
				array('time','time',1,'function'),
				//array('status','1'),  // 新增的时候把status字段设置为1
				//array('id','htmlspecialchars',3,'function') ,
				array('config_tel','htmlspecialchars',3,'function') ,
				array('config_weburl','htmlspecialchars',3,'function') ,
				
				array('config_cp','htmlspecialchars',3,'function') , //对config_cp字段在新增的时候使md5函数处理
				array('config_company','htmlspecialchars',3,'function') ,
				array('config_webkw','htmlspecialchars',2,'function') , 
				array('config_webname','htmlspecialchars',3,'function') ,
				array('config_webtitle','htmlspecialchars',3,'function') ,
				array('config_address','htmlspecialchars',3,'function') ,
				array('config_qq','htmlspecialchars',3,'function') ,
				array('config_powerby','htmlspecialchars',3,'function') ,
				array('config_name','htmlspecialchars',3,'function') ,
				array('config_icp','htmlspecialchars',3,'function') ,
				//array('password','md5',1,'function') , //对password字段在新增的时候使md5函数处理
		);

		
	}

?>