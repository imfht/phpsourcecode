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
class GuestbookModel extends RelationModel{//继承relation
		/**
		 * 自动验证
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				//array('username','require','用户必须填写!'), //成功验证用户名是否填写
				//array('username','','用户已经存在',0,'unique',1), //成功验证是否存在用户名
					
				//array('password','require','密码必须填写!'), //成功验证密码是否填写
				//array('repassword','password','确认密码不正确',0,'confirm'),
				array('gb_title','require','标题必须填写'),

				array('gb_content','require',' 内容必须填写'),
				array('gb_content','checkcontent','内容字数不够',0,'callback',0),
				
				array('gb_name','require','姓名必须填写'),
				array('gb_email','require','E-mail必须填写'),
				
				//array('content','/^\w{3,}$/','内容必须6个字母以上',0,'regex',1), //成功验证
				//array('username','/^\w{2,}$/','用户名必须6个字母以上',0,'regex',1), //成功验证
				array('gb_tel','checktel','电话检测失败',0,'callback',0),
				array('gb_qq','checkqq','QQ检测失败',0,'callback',0),
				array('gb_email','checkemail','E-mail检测失败',0,'callback',0),

				array('verify','require','验证码必须填写!'),
				array('verify','checkCode','验证码错误!',0,'callback',1),
		);
		
		/**
		 * 处理checkcontent回调函数 
		 */
		protected function checkcontent(){
			$content=$_POST['gb_content'];
			$len=strlen($content);
			if($len >= 20){
				
				return true;
			}else{
				return false;
			}
		}
		
		/**
		 * 处理checkemail回调函数
		 */
		protected function checkemail(){
			$email=$_POST['gb_email'];
			if(!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)){
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * 处理checktel回调函数 
		 */
		protected function checktel(){
			$tel=$_POST['gb_tel'];
			if(!preg_match("/^\d+$/i", $tel)){
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * 处理checkqq回调函数
		 */
		protected function checkqq(){
			$tel=$_POST['gb_qq'];
			if(!preg_match("/^\d+$/i", $tel)){
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * 判断验证码是否正确
		 */
		protected function checkCode($verify){
			if(!check_verify($verify)){
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * 关联模型
		 */
		protected $_link = array(
				'Model'=>array( //dept可以随便取名字
						'mapping_type' => self::BELONGS_TO,//这里跟3.1有点不一样
						'class_name' => 'Model', //要关联的模型类名(即表名)
		
						'mapping_name'=> 'id',//关联的映射名称，用于获取数据用(附表的关联字段)
						'foreign_key'=>'column_type', //关联的外键Id(主表的关联字段)
		
						'mapping_fields'=>array('model_table'), //关联要查询的字段
						'as_fields'=>'model_table:url', //直接把关联的字段值映射成数据对象中的某个字段
				),
		);
		
		/**
		 * 自动完成，在create时自动执行
		 */
		//array('填充字段','填充内容','填充条件','附加规则');
		//填充字段
		protected $_auto=array(
				array('gb_addtime','time',1,'function'),
				//array('status','1'),  // 新增的时候把status字段设置为1
				
				array('gb_name','htmlspecialchars',1,'function'),
				//array('content','htmlspecialchars',3,'function'), 
				array('gb_title','htmlspecialchars',3,'function'),
				array('gb_recontent','htmlspecialchars',2,'function'),
				
				array('gb_ip','htmlspecialchars',2,'function'),
				array('gb_tel','htmlspecialchars',2,'function'),
				array('gb_qq','htmlspecialchars',2,'function'),
				array('gb_email','htmlspecialchars',2,'function'),
				//array('addtime','htmlspecialchars',2,'function'),
				//array('replytime','htmlspecialchars',2,'function'),
				array('gb_reply','htmlspecialchars',2,'function'),
				array('gb_dell','htmlspecialchars',2,'function'),
				
				//对content字段在新增的时候使md5函数处理
				//array('password','md5',1,'function') , 
		);
		
	}

?>