<?php

namespace Addons\Ads;
use Common\Controller\Addon;
use Think\Db;
/**
 * 广告插件
 */

    class AdsAddon extends Addon{

        public $info = array(
            'name'=>'Ads',
            'title'=>'广告管理',
            'description'=>'广告插件',
            'status'=>1,
            'author'=>'JYmusic',
            'description' => '投放广告管理插件',
            'version'=>'0.1'
        );
        
        public $addon_path = './Addons/Ads/';
        
        /**
         * 配置列表页面
         * @var unknown_type
         */
        public $admin_list = array(
        		'listKey' => array(
        				'title'=>'广告名称',
        				'positiontext'=>'广告位置',
        				'link'=>'连接地址',
        				'statustext'=>'显示状态',
        				'level'=>'优先级',
        				'create_time'=>'开始时间',
        				'end_time'=>'结束时间',
        		),
        		'model'=>'Ads',
        		'order'=>'level asc,id asc'
        );
        public $custom_adminlist = 'adminlist.html';
 
        /**
         * (non-PHPdoc)
         * 安装函数
         * @see \Common\Controller\Addons::install()
         */
		public function table_name(){
			$db_prefix = C('DB_PREFIX');
			return $db_prefix;
		}
		
		
        public function install(){
        	$sql=<<<SQL
CREATE TABLE IF NOT EXISTS `{$this->table_name()}advertising` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '广告位置名称',
  `type` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '广告位置展示方式  0为默认展示一张',
  `width` char(20) NOT NULL DEFAULT '' COMMENT '广告位置宽度',
  `height` char(20) NOT NULL DEFAULT '' COMMENT '广告位置高度',
  `mark` char(140) NOT NULL DEFAULT '' COMMENT '广告位置标示',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='广告位置表';
SQL;

             $sql2=<<<SQLT
CREATE TABLE IF NOT EXISTS `{$this->table_name()}ads` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '广告名称',
  `position` int(11) NOT NULL COMMENT '广告位置',
  `advspic` int(11) NOT NULL COMMENT '图片地址',
  `advstext` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '文字广告内容',
  `advshtml` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '代码广告内容',
  `link` char(140) NOT NULL DEFAULT '' COMMENT '链接地址',
  `level` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '优先级',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='广告表';
SQLT;
            D()->execute($sql);
            if(count(M()->query("SHOW TABLES LIKE '".$this->table_name()."advertising'")) != 1){
                session('addons_install_error', ',AdvsType表未创建成功，请手动检查插件中的sql，修复后重新安装');
                return false;
            }
            D()->execute($sql2);
            
                    /* 先判断插件需要的钩子是否存在 */
	        $this->getisHook($this->info['name'], $this->info['name'], $this->info['description']);
	        return true;
        
        }

        /**
         * (non-PHPdoc)
         * 卸载函数
         * @see \Common\Controller\Addons::uninstall()
         */
        public function uninstall(){
	      	//删除钩子
			$model = D();
	        $this->deleteHook($this->info['name']);
			$db_prefix = C('DB_PREFIX');
			$model->execute("DROP TABLE IF EXISTS {$db_prefix}Advertising;");
			$model->execute("DROP TABLE IF EXISTS {$db_prefix}Ads;");
            return true;
        }   

        //实现的广告钩子
        public function Ads($param){
        	$list = D('Addons://Ads/Ads')->AdvsList($param);
        	if(!$list) return ;
			$this->assign('list',$list);
			$this->display('widget');
        	
        }        
}