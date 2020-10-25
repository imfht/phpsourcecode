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
class AdvertModel extends RelationModel{//继承relation
		/**
		 * 自动验证    by TuziCMS
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				//array('username','require','用户必须填写!'), //成功验证用户名是否填写
				array('advert_nav','require','所属分类必须填写'),
				array('advert_name','require','附件名称必须填写'),
				array('advert_show','require','显示状态必须填写'),
				
				//array('username','/^\w{2,}$/','用户名必须6个字母以上',0,'regex',1), //成功验证
				//array('advert_url','checkurl','图片链接检测失败',0,'callback',3),
		);
		
		/**
		 * 处理checkurl回调函数      by TuziCMS
		 */
// 		protected function checkurl(){
// 			$content=$_POST['advert_url'];
// 			if(preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/", $content)){
// 				return true;
// 			}else{
// 				return false;
// 			}
// 		}
		
		
		/**
		 * 关联模型     by TuziCMS
		 */
		protected $_link = array(
				'Adnav'=>array( //dept可以随便取名字
						'mapping_type' => self::BELONGS_TO,//这里跟3.1有点不一样
						'class_name' => 'Adnav', //要关联的模型类名(即表名)
		
						'mapping_name'=> 'id',//关联的映射名称，用于获取数据用(附表的关联字段)
						'foreign_key'=>'advert_nav', //关联的外键Id(主表的关联字段)
		
						'mapping_fields'=>array('adnav_name'), //关联要查询的字段
						'as_fields'=>'adnav_name', //直接把关联的字段值映射成数据对象中的某个字段
				),
		);
		
		
		
		/**
		 * 自动完成，在create时自动执行     by TuziCMS
		 */
		protected $_auto=array(
				//array('id','htmlspecialchars',3,'function'),
// 				array('advert_nav','htmlspecialchars',3,'function'),
// 				array('advert_image','htmlspecialchars',3,'function'),
// 				//array('advert_time','htmlspecialchars',3,'function'),
// 				array('advert_name','htmlspecialchars',3,'function'),
// 				array('advert_size','htmlspecialchars',3,'function'),
// 				array('advert_show','htmlspecialchars',3,'function'),
// 				array('advert_sort','htmlspecialchars',3,'function'),
// 				array('advert_url','htmlspecialchars',3,'function'),
		);

		
	}

?>