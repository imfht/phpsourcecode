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
class AdnavModel extends RelationModel{//继承relation
		/**
		 * 自动验证   by TuziCMS
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				//array('username','require','用户必须填写!'), //成功验证用户名是否填写
				array('adnav_name','require','分类名称必须填写'),
		);

		
		/**
		 * 自动完成，在create时自动执行   by TuziCMS
		 */
		protected $_auto=array(
				//array('id','htmlspecialchars',3,'function'),
				array('adnav_time','time',1,'function'),
				array('adnav_name','htmlspecialchars',3,'function'),
		);

		
	}

?>