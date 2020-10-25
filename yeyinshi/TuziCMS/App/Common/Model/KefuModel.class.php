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
class KefuModel extends RelationModel{//继承relation
		/**
		 * 自动验证
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				//array('username','require','用户必须填写!'), //成功验证用户名是否填写
				array('kefu_if','require','开启状态必须填写'),
				array('kefu_qq','require','客服号码必须填写'),
				array('kefu_tel','require','电话号码必须填写'),
				array('kefu_tel','checktel','电话号码检测失败',0,'callback',1),
		);

		/**
		 * 处理checktel回调函数      TUZICMS
		 */
		protected function checktel(){
			$content=$_POST['user_tel'];
			if(!preg_match("/1[3458]{1}\d{9}$/",$content)){
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
				array('kefu_if','htmlspecialchars',3,'function'), 
				array('kefu_qq','htmlspecialchars',3,'function'),
				array('kefu_tel','htmlspecialchars',3,'function'),
		);
		
	}

?>