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
class NewsModel extends RelationModel{//继承relation
		/**
		 * 自动验证
		 */
		protected $_validate=array(
				//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
				array('nv_id','require','所属栏目必须填写'),
				array('news_title','require','标题必须填写'),
				array('news_content','require',' 内容必须填写'),
				array('news_author','require','作者必须填写!'),
				array('news_hits','checkhits','点击次数检测失败',0,'callback',3),
				array('news_dell','checkdell','是否删除检测失败',0,'callback',3),
				array('news_type','checktype','鉴定类型检测失败',0,'callback',3),
				//array('news_download','checkurl','下载地址检测失败',0,'callback',3),
		);
		
		/**
		 * 处理checkurl回调函数
		 */
		protected function checkurl(){
			$content=$_POST['news_download'];
			if(preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/", $content)){
				return true;
			}else{
				return false;
			}
		}
		
		/**
		 * 处理checksort回调函数
		 */
		protected function checkhits(){
			$content=$_POST['news_hits'];
			if(!preg_match("/^\d+$/i", $content)){
				return false;
			}else{
				return true;
			}
		}
		/**
		 * 处理checkdell回调函数
		 */
		protected function checkdell(){
			$content=$_POST['news_dell'];
			if(!preg_match("/^\d+$/i", $content)){
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * 处理checktype回调函数
		 */
		protected function checktype(){
			$content=$_POST['news_type'];
			if(!preg_match("/^\d+$/i", $content)){
				return false;
			}else{
				return true;
			}
		}
		
		
		/**
		 * 关联模型
		 */
		protected $_link = array(
// 				'Attr'=>array( //dept可以随便取名字
// 						'mapping_type' => self::BELONGS_TO,//这里跟3.1有点不一样
// 						'class_name' => 'Attr', //要关联的模型类名(即表名)
		
// 						'mapping_name'=> 'id',//关联的映射名称，用于获取数据用(附表的关联字段)
// 						'foreign_key'=>'news_type', //关联的外键Id(主表的关联字段)
		
// 						'mapping_fields'=>array('attr_name,attr_color'), //关联要查询的字段
// 						'as_fields'=>'attr_name,attr_color', //直接把关联的字段值映射成数据对象中的某个字段
// 				),
				
				'Column'=>array( //dept可以随便取名字
						'mapping_type' => self::BELONGS_TO,//这里跟3.1有点不一样
						'class_name' => 'Column', //要关联的模型类名(即表名)
				
						'mapping_name'=> 'id',//关联的映射名称，用于获取数据用(附表的关联字段)
						'foreign_key'=>'nv_id', //关联的外键Id(主表的关联字段)
				
						'mapping_fields'=>array('column_name'), //关联要查询的字段
						'as_fields'=>'column_name', //直接把关联的字段值映射成数据对象中的某个字段
				),
				
				
				'News_Attr' => array( //dept可以随便取名字
						'mapping_type'      =>  self::MANY_TO_MANY,//这里跟3.1有点不一样
						'class_name'        =>  'Attr',//要关联的模型类名(即表名)
						'mapping_name'      =>  'child',//关联的映射名称，用于获取数据用(附表的关联字段)
						'foreign_key'       =>  'news_id',//关联的外键Id(主表的关联字段)
						'relation_foreign_key'  =>  'attr_id',//关联的外键Id(主表的关联字段)
						'relation_table'    =>  'tuzi_attr_news' //此处应显式定义中间表名称，且不能使用C函数读取表前缀
				),
		);
		
		
		
		/**
		 * 自动完成，在create时自动执行
		 * array('填充字段','填充内容','填充条件','附加规则')
		 */
		protected $_auto=array(
				array('news_addtime','time',1,'function'),
				//array('status','1'),  // 新增的时候把status字段设置为1
				//array('news_content','htmlspecialchars',3,'function'),
				array('news_title','htmlspecialchars',3,'function'),
				array('news_keywords','htmlspecialchars',3,'function'),
				array('news_description','htmlspecialchars',3,'function'),
				array('news_author','htmlspecialchars',3,'function'),
				
				//array('id','htmlspecialchars',3,'function'),
				//array('nv_id','htmlspecialchars',3,'function'),
				array('news_hits','htmlspecialchars',3,'function'),
				//array('news_updatetime','htmlspecialchars',3,'function'),
				//array('news_addtime','htmlspecialchars',3,'function'),
				array('news_type','htmlspecialchars',3,'function'),
				array('news_dell','htmlspecialchars',3,'function'),
				array('news_images','htmlspecialchars',3,'function'),
				array('news_sort','htmlspecialchars',3,'function'),
				array('news_pic','htmlspecialchars',3,'function'),
				array('news_download','htmlspecialchars',3,'function'),

		);

		/**
		 * 字段映射
		 */
		protected $_map = array(
				'title' =>'news_title', //把表单中title映射到数据表的news_title字段
				'keywords'  =>'news_keywords',
		);
		
		
	}

?>