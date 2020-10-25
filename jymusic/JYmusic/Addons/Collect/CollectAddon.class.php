<?php
/**
 * JYmusic音乐采集插件
 * @author JYmusic
 * email 378020023@qq.com
 *
 */
namespace Addons\Collect;
use Common\Controller\Addon;
use Think\Db;
use Think\Model;

/**
 * JYmusic音乐采集插件
 * 
 */
class CollectAddon extends Addon {
	public $info = array (
			'name' => 'Collect',
			'title' => 'JYmusic采集器',
			'description' => '只是简单的采集而已，欢迎自己扩展',
			'status' => 1,
			'author' => 'JYmusic',
			'version' => '0.1' 
	);
	public $addon_path = './Addons/Collect/';
	public $admin_list = array (
			'model' => 'CollectRule', // 要查的表
			'fields' => '*', // 要查的字段
			'map' => '', // 查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
			'order' => 'create_time desc', // 排序,
			'listKey' => array ( // 这里定义的是除了id序号外的表格里字段显示的表头名
					'rule_name' => '规则名称',
					'page_rule' => '分页规则',
					'first_page_id' => '开始ID',
					'last_page_id' => '结束ID',
					//'link_rule' => '单页链接规则',
					//'title_rule' => '标题规则',					
					//'play_rule' => '播放地址规则',
					//'encode' => '页面编码',
					'server_id' => '服务器ID',
					'create_time' => '更新时间' 
			) 
	);
	public $custom_adminlist = 'rulelist.html';
	public function install() {
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$this->db_prefix()}collect_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_name` varchar(100) NOT NULL,
  `page_rule` varchar(200) NOT NULL COMMENT '分页规则',
  `first_page_id` smallint(5) NOT NULL DEFAULT '1' COMMENT '起始分页id',
  `last_page_id` smallint(5) NOT NULL DEFAULT '5' COMMENT '结束分页Id',
  `link_wrap_rule` text NOT NULL COMMENT '链接外部规则',
  `link_rule` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '连接规则',
  `title_rule` text NOT NULL COMMENT '音乐标题规则',
  `play_rule` text NOT NULL COMMENT '播放地址规则',
  `play_rule2` text COMMENT '播放地址规则2',
  `encode` varchar(16) NOT NULL DEFAULT 'utf-8' COMMENT '页面编码',
  `server_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '服务器ID',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL;
		
	D()->execute($sql);
	if (M("hooks")->where ( "name='Collect' AND type=1" )->find ()) {
		return true;
	}
	M("hooks")->add(array('name'=>'Collect','description'=>'JYmusic采集','type'=>'1','addons'=>'Collect','update_time'=>time()));
		return true;
	}
	public function uninstall() {
		$model = D();
        $model->execute("DROP TABLE IF EXISTS {$this->db_prefix()}collect_rule;");
		return true;
	}
	public function db_prefix(){
		$db_prefix = C('DB_PREFIX');
		return $db_prefix;
	}
	// 实现的pageHeader钩子方法
	public function Collect($param) {

	}
}