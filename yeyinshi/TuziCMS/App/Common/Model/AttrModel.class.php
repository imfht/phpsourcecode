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
class AttrModel extends RelationModel{//继承relation
		/**
		 * 自动验证       by TuziCMS
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				//array('username','','用户已经存在',0,'unique',1), //成功验证是否存在用户名
				//array('repassword','password','确认密码不正确',0,'confirm'),
				array('attr_name','require','鉴定名称必须填写'),
				array('attr_color','require','鉴定颜色必须填写'),

				//array('verify','checkCode','验证码错误!',0,'callback',1),
				//array('username','/^\w{2,}$/','用户名必须6个字母以上',0,'regex',1), //成功验证
				//array('verify','checkun','用户名错误!',0,'callback',1),
		);
			
		
		/**
		 * 自动完成密码md5加密和危险字符过滤处理      by TuziCMS
		 */
		protected $_auto = array (
				//array('password','md5',1,'function') , //对password字段在新增的时候使md5函数处理
				//array('id','htmlspecialchars',3,'function'),
				array('attr_name','htmlspecialchars',3,'function'),
				array('attr_color','htmlspecialchars',3,'function'),
				
		);
		
		/**
		 * 关联模型     by TuziCMS
		 */
		protected $_link = array(
				'News_Attr' => array( //dept可以随便取名字
						'mapping_type'      =>  self::MANY_TO_MANY,//这里跟3.1有点不一样
						'class_name'        =>  'News',//要关联的模型类名(即表名)
						'mapping_name'      =>  'child',//关联的映射名称，用于获取数据用(附表的关联字段)
						'foreign_key'       =>  'attr_id',//关联的外键Id(主表的关联字段)
						'relation_foreign_key'  =>  'news_id',//关联的外键Id(主表的关联字段)
						'relation_table'    =>  'tuzi_attr_news' //此处应显式定义中间表名称，且不能使用C函数读取表前缀
			),
		);
		

	}
?>

