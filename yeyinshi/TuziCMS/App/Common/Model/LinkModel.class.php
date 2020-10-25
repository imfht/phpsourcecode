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
class LinkModel extends RelationModel{//继承relation
		/**
		 * 自动验证
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				//array('username','require','用户必须填写!'), //成功验证用户名是否填写
				array('link_url','require','链接地址必须填写'),
				array('link_url','checkurl','网站URL检测失败',0,'callback',3),
				
				array('link_name','require','链接名称必须填写'),
				//array('username','/^\w{2,}$/','用户名必须6个字母以上',0,'regex',1), //成功验证
				array('link_sort','checksort','排列位置检测失败',0,'callback',3),
		);
		
		/**
		 * 处理checkurl回调函数      TUZICMS
		 */
		protected function checkurl(){
			$content=$_POST['link_url'];
			if(preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/", $content)){
				return true;
			}else{
				return false;
			}
		}
		
		/**
		 * 处理checksort回调函数      TUZICMS
		 */
		protected function checksort(){
			$content=$_POST['link_sort'];
			if(!preg_match("/^\d+$/i", $content)){
				return false;
			}else{
				return true;
			}
		}
		
		
		/**
		 * 自动完成，在create时自动执行
		 */
		//array('填充字段','填充内容','填充条件','附加规则');
		//填充字段
		protected $_auto=array(
				//array('gb_addtime','time',1,'function'),
				//array('status','1'),  // 新增的时候把status字段设置为1
				array('link_description','htmlspecialchars',3,'function'),
				array('link_name','htmlspecialchars',3,'function'),
				
				//array('id','htmlspecialchars',3,'function'),
				array('link_url','htmlspecialchars',3,'function'),
				array('link_sort','htmlspecialchars',3,'function'),
				array('link_show','htmlspecialchars',3,'function'),
				//array('link_addtime','htmlspecialchars',3,'function'),
		);

		
	}

?>