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
class SpecialModel extends RelationModel{//继承relation
		/**
		 * 自动验证
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				//array('username','require','用户必须填写!'), //成功验证用户名是否填写
				array('special_title','require','专题标题必须填写'),
				array('special_template','require','专题模板必须选择'),
		);

		
		/**
		 * 自动完成，在create时自动执行
		 * array('填充字段','填充内容','填充条件','附加规则');
		 */
		protected $_auto=array(
				array('special_addtime','time',1,'function'),
				array('special_title','htmlspecialchars',3,'function'),
				array('special_keywords','htmlspecialchars',3,'function'),
				array('special_description','htmlspecialchars',3,'function'),
				array('special_template','htmlspecialchars',3,'function'),
				
				//array('id','htmlspecialchars',3,'function'),
				//array('notice_time','htmlspecialchars',3,'function'),
		);

		
	}

?>