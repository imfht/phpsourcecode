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
class ModelModel extends RelationModel{//继承relation
		/**
		 * 自动验证
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				//array('username','require','用户必须填写!'), //成功验证用户名是否填写
				array('model_name','require','模型名字必须填写'),
				array('model_table','require','控制器名称必须填写'),
				array('model_table','checktable','控制器名称检测失败',0,'callback',3),
		);
		
		/**
		 * 处理checktable回调函数
		 */
		protected function checktable(){
			$content=$_POST['link_sort'];
			if(preg_match('/^[a-zA-Z]+$/',$content)){
				return false;
			}else{
				return true;
			}
		}
		
		
		/**
		 * 自动完成，在create时自动执行
		 * array('填充字段','填充内容','填充条件','附加规则');
		 */
		protected $_auto=array(
				//array('gb_addtime','time',1,'function'),
				//array('status','1'),  // 新增的时候把status字段设置为1
				array('model_name','htmlspecialchars',3,'function'),
				array('model_table','htmlspecialchars',3,'function'),
				
				//array('id','htmlspecialchars',3,'function'),
				array('model_list','htmlspecialchars',3,'function'),
				array('model_show','htmlspecialchars',3,'function'),
				//array('model_addtime','htmlspecialchars',3,'function'),
		);

		
	}

?>