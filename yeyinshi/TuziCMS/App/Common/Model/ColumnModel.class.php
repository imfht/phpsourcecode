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
class ColumnModel extends RelationModel{//继承relation
		/**
		 * 自动验证    by TuziCMS
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				array('column_name','require','栏目名称必须填写!'), //成功验证栏目名称是否填写
				array('column_link','require','栏目链接必须填写!'), //成功验证栏目名称是否填写
				array('column_ename','','该栏目别名已经存在！',0,'unique',1), //验证是否存在栏目别名
				array('column_ename','','该栏目别名已经存在！',0,'unique',2), //验证是否存在栏目别名
				array('column_keyw','require','关键词必须填写!'),
				array('column_type','require','内容模型必须填写!'),
				array('f_id','require','所属栏目必须填写!'),
				array('column_sort','require','栏目排序必须填写!'),
				//array('user_name','','该用户名已经存在！',0,'unique',1), //验证是否存在用户名
				//array('user_repass','require','确认密码必须填写'),
				//array('user_repass','user_pass','确认密码不正确',0,'confirm'),
				//array('username','/^\w{2,}$/','用户名必须6个字母以上',0,'regex',1), //成功验证
		);
		/**
		 * 模型关联     by TuziCMS
		 */
		protected $_link = array(
				'Dept'=>array( //dept可以随便取名字
						'mapping_type' => self::BELONGS_TO,//这里跟3.1有点不一样
						'class_name' => 'Model', //要关联的模型类名(即表名)
		
						'mapping_name'=> 'id',//关联的映射名称，用于获取数据用(附表的关联字段)
						'foreign_key'=>'column_type', //关联的外键Id(主表的关联字段)
		
						'mapping_fields'=>array('model_table'), //关联要查询的字段
						'as_fields'=>'model_table:url', //直接把关联的字段值映射成数据对象中的某个字段
				),
		);
		
		/**
		 * 自动完成，在create时自动执行   by TuziCMS
		 */
		protected $_auto=array(
				//array('填充字段','填充内容','填充条件','附加规则');
				//array('news_addtime','time',1,'function'),
				//array('status','1'),  // 新增的时候把status字段设置为1
				
				//array('id','htmlspecialchars',1,'function'),//新增的时候就过滤处理
				array('f_id','htmlspecialchars',1,'function'),
				array('column_name','htmlspecialchars',3,'function'),
				array('column_ename','htmlspecialchars',3,'function'),
				array('column_keyw','htmlspecialchars',3,'function'),
				array('column_descr','htmlspecialchars',1,'function'),
				array('column_descr','htmlspecialchars',2,'function'),
				array('column_content','htmlspecialchars',1,'function'),
				
				array('column_type','htmlspecialchars',3,'function'),
				//array('column_addtime','htmlspecialchars',3,'function'),
				array('column_sort','htmlspecialchars',3,'function'),
				array('column_status','htmlspecialchars',3,'function'),
				array('column_link','htmlspecialchars',3,'function'),
				array('column_url','htmlspecialchars',3,'function'),
				array('column_ifimg','htmlspecialchars',3,'function'),
				array('column_images','htmlspecialchars',3,'function'),
				//array('password','md5',1,'function') , //对password字段在新增的时候使md5函数处理
		);

		
	}

?>