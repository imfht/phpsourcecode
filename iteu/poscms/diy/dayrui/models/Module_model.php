<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Module_model extends CI_Model {
	
	public $system_table; // 系统默认表
	
	/*
	 * 模块模型类
	 */
    public function __construct() {
        parent::__construct();
		$this->system_table = array(
			'draft' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
			  `eid` int(10) DEFAULT NULL COMMENT '扩展id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
			  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
			  `content` mediumtext NOT NULL COMMENT '具体内容',
			  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
			  PRIMARY KEY `id` (`id`),
			  KEY `eid` (`eid`),
			  KEY `uid` (`uid`),
			  KEY `cid` (`cid`),
			  KEY `catid` (`catid`),
			  KEY `inputtime` (`inputtime`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='内容草稿表';",

			'verify' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL,
			  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
			  `author` varchar(50) NOT NULL COMMENT '作者',
	          `status` tinyint(2) NOT NULL COMMENT '审核状态',
			  `content` mediumtext NOT NULL COMMENT '具体内容',
			  `backuid` mediumint(8) unsigned NOT NULL COMMENT '操作人uid',
			  `backinfo` text NOT NULL COMMENT '操作退回信息',
			  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
			  UNIQUE KEY `id` (`id`),
			  KEY `uid` (`uid`),
			  KEY `catid` (`catid`),
			  KEY `status` (`status`),
			  KEY `inputtime` (`inputtime`),
			  KEY `backuid` (`backuid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='内容审核表';",

			'hits' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL COMMENT '文章id',
			  `hits` int(10) unsigned NOT NULL COMMENT '总点击数',
			  `day_hits` int(10) unsigned NOT NULL COMMENT '本日点击',
			  `week_hits` int(10) unsigned NOT NULL COMMENT '本周点击',
			  `month_hits` int(10) unsigned NOT NULL COMMENT '本月点击',
			  `year_hits` int(10) unsigned NOT NULL COMMENT '年点击量',
			  UNIQUE KEY `id` (`id`),
			  KEY `day_hits` (`day_hits`),
			  KEY `week_hits` (`week_hits`),
			  KEY `month_hits` (`month_hits`),
			  KEY `year_hits` (`year_hits`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='时段点击量统计';",
			
			'index' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
			  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
	          `status` tinyint(2) NOT NULL COMMENT '审核状态',
			  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
			  PRIMARY KEY (`id`),
			  KEY `uid` (`uid`),
			  KEY `catid` (`catid`),
			  KEY `status` (`status`),
			  KEY `inputtime` (`inputtime`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='内容索引表';",
			
			'extend_index' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
			  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
	          `status` tinyint(2) NOT NULL COMMENT '审核状态',
			  PRIMARY KEY (`id`),
			  KEY `cid` (`cid`),
			  KEY `uid` (`uid`),
			  KEY `catid` (`catid`),
			  KEY `status` (`status`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='扩展索引表';",

            'extend_verify' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL,
			  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
			  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
			  `author` varchar(50) NOT NULL COMMENT '作者',
	          `status` tinyint(2) NOT NULL COMMENT '审核状态',
			  `content` mediumtext NOT NULL COMMENT '具体内容',
			  `backuid` mediumint(8) unsigned NOT NULL COMMENT '操作人uid',
			  `backinfo` text NOT NULL COMMENT '操作退回信息',
			  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
			  UNIQUE KEY `id` (`id`),
			  KEY `cid` (`cid`),
			  KEY `uid` (`uid`),
			  KEY `catid` (`catid`),
			  KEY `status` (`status`),
			  KEY `inputtime` (`inputtime`),
			  KEY `backuid` (`backuid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='扩展内容审核表';",
			
			'category' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`pid` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
				`pids` varchar(255) NOT NULL COMMENT '所有上级id',
				`name` varchar(30) NOT NULL COMMENT '栏目名称',
				`letter` char(1) NOT NULL COMMENT '首字母',
				`dirname` varchar(30) NOT NULL COMMENT '栏目目录',
				`pdirname` varchar(100) NOT NULL COMMENT '上级目录',
				`child` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有下级',
				`childids` text NOT NULL COMMENT '下级所有id',
				`thumb` varchar(255) NOT NULL COMMENT '栏目图片',
				`show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
				`permission` text NULL COMMENT '会员权限',
				`setting` text NOT NULL COMMENT '属性配置',
				`displayorder` tinyint(3) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `show` (`show`),
				KEY `module` (`pid`,`displayorder`,`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='栏目表';",
			
			'category_data' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
			  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
			  PRIMARY KEY (`id`),
			  KEY `uid` (`uid`),
			  KEY `catid` (`catid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='栏目附加表';",
			
			'category_data_0' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
			  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
			  PRIMARY KEY (`id`),
			  KEY `uid` (`uid`),
			  KEY `catid` (`catid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='栏目附加表';",
			
			'tag' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(200) NOT NULL COMMENT 'tag名称',
			  `code` varchar(200) NOT NULL COMMENT 'tag代码（拼音）',
			  `hits` mediumint(8) unsigned NOT NULL COMMENT '点击量',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `name` (`name`),
			  KEY `letter` (`code`,`hits`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Tag标签表';
			",
			
			'flag' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `flag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '文档标记id',
			  `id` int(10) unsigned NOT NULL COMMENT '文档内容id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
			  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
			  KEY `flag` (`flag`,`id`,`uid`),
			  KEY `catid` (`catid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标记表';
			",
			
			'search' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` varchar(32) NOT NULL,
			  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
			  `params` text NOT NULL COMMENT '参数数组',
			  `keyword` varchar(255) NOT NULL COMMENT '关键字',
			  `contentid` mediumtext NOT NULL COMMENT 'id集合',
			  `inputtime` int(10) unsigned NOT NULL COMMENT '搜索时间',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`),
			  KEY `catid` (`catid`),
			  KEY `keyword` (`keyword`),
			  KEY `inputtime` (`inputtime`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='搜索表';
			",

			'search_index' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` varchar(32) NOT NULL,
			  `cid` int(10) unsigned NOT NULL COMMENT '文档Id',
			  `inputtime` int(10) unsigned NOT NULL COMMENT '搜索时间',
			  KEY (`id`),
			  KEY `cid` (`cid`),
			  KEY `inputtime` (`inputtime`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='搜索索引表';
			",
			
			'html' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` bigint(18) unsigned NOT NULL AUTO_INCREMENT,
			  `rid` int(10) unsigned NOT NULL COMMENT '相关id',
			  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
			  `type` tinyint(1) unsigned NOT NULL COMMENT '文件类型',
			  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
			  `filepath` text NOT NULL COMMENT '文件地址',
			  PRIMARY KEY (`id`),
			  KEY `uid` (`uid`),
			  KEY `rid` (`rid`),
			  KEY `cid` (`cid`),
			  KEY `type` (`type`),
			  KEY `catid` (`catid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='html文件存储表';",
			
			'favorite' => "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
              `cid` int(10) unsigned NOT NULL COMMENT '文档id',
              `eid` int(10) unsigned DEFAULT NULL COMMENT '扩展id',
              `uid` mediumint(8) unsigned NOT NULL COMMENT 'uid',
              `url` varchar(255) NOT NULL COMMENT 'URL地址',
              `title` varchar(255) NOT NULL COMMENT '标题',
              `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
              PRIMARY KEY (`id`),
              KEY `uid` (`uid`),
              KEY `cid` (`cid`),
              KEY `eid` (`eid`),
              KEY `inputtime` (`inputtime`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收藏夹表';",

			'buy' => "
            CREATE TABLE IF NOT EXISTS `{tablename}` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
              `cid` int(10) unsigned NOT NULL COMMENT '文档id',
              `uid` mediumint(8) unsigned NOT NULL COMMENT 'uid',
              `title` varchar(255) NOT NULL COMMENT '标题',
              `thumb` varchar(255) NOT NULL COMMENT '缩略图',
              `url` varchar(255) NOT NULL COMMENT 'URL地址',
              `score` int(10) unsigned NOT NULL COMMENT '使用虚拟币',
              `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
              PRIMARY KEY (`id`),
              KEY `cid` (`cid`,`uid`,`inputtime`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='主题购买记录表';",

			'extend_buy' => "
            CREATE TABLE IF NOT EXISTS `{tablename}` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
              `cid` int(10) unsigned NOT NULL COMMENT '文档id',
              `eid` int(10) unsigned NOT NULL COMMENT '扩展id',
              `uid` mediumint(8) unsigned NOT NULL COMMENT 'uid',
              `title` varchar(255) NOT NULL COMMENT '标题',
              `thumb` varchar(255) NOT NULL COMMENT '缩略图',
              `url` varchar(255) NOT NULL COMMENT 'URL地址',
              `score` int(10) unsigned NOT NULL COMMENT '使用虚拟币',
              `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
              PRIMARY KEY (`id`),
              KEY `cid` (`cid`,`eid`,`uid`,`inputtime`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='扩展购买记录表';",

		);	
	}
	
	/**
	 * 所有模块
	 *
	 * @return	array
	 */
	public function get_data() {

		$_data = $this->db->order_by('displayorder ASC,id ASC')->get('module')->result_array();
		if (!$_data) {
            return NULL;
        }

		$data = array();
		foreach ($_data as $t) {
			$t['site'] = dr_string2array($t['site']);
			$t['setting'] = dr_string2array($t['setting']);
			$data[$t['dirname']] = $t;
		}

		return $data;
	}
	
	/**
	 * 模块数据
	 *
	 * @param	int		$id
	 * @return	array
	 */
	public function get($id) {

		if (is_numeric($id)) {
			$this->db->where('id', (int)$id);
		} else {
			$this->db->where('dirname', (string)$id);
		}
		$data = $this->db->limit(1)->get('module')->row_array();
		if (!$data) {
            return NULL;
        }

		$data['site'] = dr_string2array($data['site']);
		$data['setting'] = dr_string2array($data['setting']);

        // 模块名称
        $name = $this->db->select('name')->where('pid', 0)->where('mark', 'module-'.$data['dirname'])->get('admin_menu')->row_array();
        $data['name'] = $name['name'] ? $name['name'] : $data['dirname'];

		return $data;
	}
	
	/**
	 * 模块入库
	 *
	 * @param	string	$dir
	 * @return	intval
	 */
	public function add($dir, $config, $nodb = '') {

		if (!$dir) {
            return NULL;
        } elseif ($this->db->where('dirname', $dir)->count_all_results('module')) {
			// 判断重复安装
            return NULL;
        }

		$share = (int)$config['share'];
		$extend = (int)$config['extend'];
		$m = array(
			'site' => '',
			'share' => $share,
			'extend' => $extend,
			'dirname' => $dir,
			'setting' => '',
			'sitemap' => 1,
			'disabled' => 0,
			'displayorder' => 0,
		);
		$this->db->replace('module', $m);
		$m['id'] = $id = $this->db->insert_id();

		if (!$id) {
            return NULL;
        }

        // 非自定义表时
        if (!$nodb) {
			// 字段入库
			$main = require FCPATH.'module/'.$dir.'/config/main.table.php'; // 主表信息
			foreach ($main['field'] as $field) {
				$this->add_field($id, $field, 1);
			}
			$data = require FCPATH.'module/'.$dir.'/config/data.table.php'; // 附表信息
			if ($data['field']) {
				foreach ($data['field'] as $field) {
					$this->add_field($id, $field, 0);
				}
			}
			//扩展内容表
			if ($extend) {
				// 字段入库
				$main = require FCPATH.'module/'.$dir.'/config/extend.main.table.php'; // 主表信息
				foreach ($main['field'] as $field) {
					$this->add_field($id, $field, 1, 1);
				}
				$data = require FCPATH.'module/'.$dir.'/config/extend.data.table.php'; // 附表信息
				if ($data['field']) {
					foreach ($data['field'] as $field) {
						$this->add_field($id, $field, 0, 1);
					}
				}
			}
        } else {
            $install_file = FCPATH.'module/'.$dir.'/config/install.php'; // 自定义安装文件
            if (is_file($install_file)) {
                $is_add = 1;
                require $install_file;
            }
        }

        // 删除后台菜单
        $this->db->where('mark', 'module-'.$dir)->delete('admin_menu');
        $this->db->like('mark', 'module-'.$dir.'-%')->delete('admin_menu');
        // 删除会员菜单
        $this->db->where('mark', 'left-'.$dir)->delete('member_menu');
        $this->db->where('mark', 'module-'.$dir)->delete('member_menu');
        $this->db->like('mark', 'module-'.$dir.'-%')->delete('member_menu');

		// 重新安装菜单
		if (is_file(FCPATH.'module/'.$dir.'/config/menu.php')) {
			// 后台菜单
			$this->load->model('menu_model');
			$this->menu_model->init_module($m);
            // 会员菜单
			$this->load->model('member_menu_model');
			$this->member_menu_model->init_module($m);
		}

		return $id;
	}
	
	// 模块的导出
	public function export($dir, $name) {
	
		if (!is_dir(FCPATH.'module/'.$dir)) {
            return '模块目录不存在';
        }
		
		// 模块信息
		$module = $this->db->limit(1)->where('dirname', $dir)->get('module')->row_array();
		if (!$module) {
            return '模块不存在或者尚未安装';
        }
        $site = dr_string2array($module['site']);
        if (!isset($site[SITE_ID]) || !$site[SITE_ID]['use']) {
            return '当前站点尚未安装此模块，无法生成';
        }

		// 模块配置文件
		$config = require FCPATH.'module/'.$dir.'/config/module.php';
        if (isset($config['nodb']) && $config['nodb']) {
            return '自定义数据表模块不允许生成';
        }

		$config['key'] = 0;
		$config['name'] = $name ? $name : $config['name'];
		$config['author'] = SITE_NAME;
		$config['version'] = '';
		$this->load->library('dconfig');
		$size = $this->dconfig->file(FCPATH.'module/'.$dir.'/config/module.php')->note('模块配置文件')->space(24)->to_require_one($config);
		if (!$size) {
            return '目录'.$dir.'不可写！';
        }

        // 主表字段
        $db = $this->db;
		$file = FCPATH.'module/'.$dir.'/config/main.table.php';
		$table = array();
		$header = $this->dconfig->file($file)->note('主表结构（由开发者定义）')->to_header();
		$sql = $db->query("SHOW CREATE TABLE `".$this->db->dbprefix(SITE_ID.'_'.$dir)."`")->row_array();
		$table['sql'] = str_replace(array($sql['Table'], 'CREATE TABLE'), array('{tablename}', 'CREATE TABLE IF NOT EXISTS'), $sql['Create Table']);
		$field = $this->db
					  ->where('relatedname', 'module')
					  ->where('relatedid', $module['id'])
					  ->where('ismain', 1)
					  ->get('field')
					  ->result_array();
		if (!$field) {
            return '此模块无主表字段，不支持生成';
        }
		foreach ($field as $t) {
			$t['textname'] = $t['name'];
			unset($t['id'], $t['name']);
			$t['issystem'] = 1;
			$t['setting'] = dr_string2array($t['setting']);
			$table['field'][] = $t;
		}
		file_put_contents($file, $header.PHP_EOL.'return '.var_export($table, true).';?>');
		
		// 附表字段
		$file = FCPATH.'module/'.$dir.'/config/data.table.php';
		$table = array();
		$header = $this->dconfig->file($file)->note('附表结构（由开发者定义）')->to_header();
		$sql = $db->query("SHOW CREATE TABLE `".$this->db->dbprefix(SITE_ID.'_'.$dir.'_data_0')."`")->row_array();
		$table['sql'] = str_replace(array($sql['Table'], 'CREATE TABLE'), array('{tablename}', 'CREATE TABLE IF NOT EXISTS'), $sql['Create Table']);
		$field = $this->db
					  ->where('relatedname', 'module')
					  ->where('relatedid', $module['id'])
					  ->where('ismain', 0)
					  ->get('field')
					  ->result_array();
		if ($field) {
			foreach ($field as $t) {
				$t['textname'] = $t['name'];
				unset($t['id'], $t['name']);
				$t['issystem'] = 1;
				$t['setting'] = dr_string2array($t['setting']);
				$table['field'][] = $t;
			}
		}
		file_put_contents($file, $header.PHP_EOL.'return '.var_export($table, true).';?>');
		
		if ($config['extend']) {
			// 内容扩展表字段
			$file = FCPATH.'module/'.$dir.'/config/extend.main.table.php';
			$table = array();
			$header = $this->dconfig->file($file)->note('内容扩展表结构（由开发者定义）')->to_header();
			$sql = $db->query("SHOW CREATE TABLE `".$this->db->dbprefix(SITE_ID.'_'.$dir.'_extend')."`")->row_array();
			$table['sql'] = str_replace(array($sql['Table'], 'CREATE TABLE'), array('{tablename}', 'CREATE TABLE IF NOT EXISTS'), $sql['Create Table']);
			$field = $this->db->where('relatedname', 'extend')->where('relatedid', $module['id'])->get('field')->result_array();
			if ($field) {
				foreach ($field as $t) {
					$t['textname'] = $t['name'];
					unset($t['id'], $t['name']);
					$t['issystem'] = 1;
					$t['setting'] = dr_string2array($t['setting']);
					$table['field'][] = $t;
				}
			}
			file_put_contents($file, $header.PHP_EOL.'return '.var_export($table, true).';?>');

            // 内容扩展附表字段
            $file = FCPATH.'module/'.$dir.'/config/extend.data.table.php';
            $table = array();
            $header = $this->dconfig->file($file)->note('内容扩展附表结构（由开发者定义）')->to_header();
            $sql = $db->query("SHOW CREATE TABLE `".$this->db->dbprefix(SITE_ID.'_'.$dir.'_extend_data_0')."`")->row_array();
            $table['sql'] = str_replace(array($sql['Table'], 'CREATE TABLE'), array('{tablename}', 'CREATE TABLE IF NOT EXISTS'), $sql['Create Table']);
            $field = $this->db->where('relatedname', 'extend')->where('relatedid', $module['id'])->get('field')->result_array();
            if ($field) {
                foreach ($field as $t) {
                    $t['textname'] = $t['name'];
                    unset($t['id'], $t['name']);
                    $t['issystem'] = 0;
                    $t['setting'] = dr_string2array($t['setting']);
                    $table['field'][] = $t;
                }
            }
            file_put_contents($file, $header.PHP_EOL.'return '.var_export($table, true).';?>');
		}

		// 导出表单
		$this->export_form($dir);
		
		return NULL;
	}
	
	// 导出表单
	public function export_form($dir) {

		$form = $this->db->where('module', $dir)->get('module_form')->result_array();
		if ($form) {
			$fdata = array();
			foreach ($form as $t) {
                $table = $this->db->dbprefix(SITE_ID.'_'.$dir.'_form_'.$t['table']);
                // 主表
				$sql = $this->db->query("SHOW CREATE TABLE `".$table."`")->row_array();
				$sql = str_replace(array($sql['Table'], 'CREATE TABLE'), array('{tablename}', 'CREATE TABLE IF NOT EXISTS'), $sql['Create Table']);
				// 附表
                $sql2 = $this->db->query("SHOW CREATE TABLE `".$table."_data_0`")->row_array();
				$sql2 = str_replace(array($sql2['Table'], 'CREATE TABLE'), array('{tablename}', 'CREATE TABLE IF NOT EXISTS'), $sql2['Create Table']);
				// 模块表单的自定义字段
				$field = $this->db->where('disabled', 0)->where('relatedid', $t['id'])->where('relatedname', 'mform-'.$dir)->order_by('displayorder ASC, id ASC')->get('field')->result_array();
				$fdata[$t['id']] = array(
					'sql' => $sql,
					'sql2' => $sql2,
					'data' => $t,
					'field' => $field,
				);
			}
			$file = FCPATH.'module/'.$dir.'/config/form.php';
			$this->load->library('dconfig');
			$header = $this->dconfig->file($file)->note('表单的结构（此文件由导出产生，无需开发者定义）')->to_header();
			file_put_contents($file, $header.PHP_EOL.'return '.var_export($fdata, true).';?>');
		}
	}
	
	// 导入表单
	public function import_form($dir, $siteid) {

		$file = FCPATH.'module/'.$dir.'/config/form.php';
		if (!is_file($file)) {
            return FALSE;
        }

        // 生成的表单配置文件
		$data = require_once $file;
		if (!$data) {
            return FALSE;
        }

        // 当前站点数据库句柄
        $db = $this->site[$siteid];
        $table = $this->db->dbprefix($siteid.'_'.$dir.'_form');

        // 循环导入配置表单
		foreach ($data as $id => $form) {
			// 插入表单
            $tablename = $form['data']['table'] ? $form['data']['table'] : ($form['data']['tablename'] ? $form['data']['tablename'] : SITE_ID.'_'.$form['data']['id']);
            unset($form['data']['id'],$form['data']['tablename']);
            $form['data']['module'] = $dir;
            $form['data']['table'] = $tablename;
			$this->db->insert('module_form', $form['data']);
            $id = $this->db->insert_id();
            // 创建主表
			$db->query('DROP TABLE IF EXISTS `'.$table.'_'.$tablename.'`');
			$db->query(trim(str_replace('{tablename}', $table.'_'.$tablename, $form['sql'])));
            if (strpos($form['sql'], 'tableid') === FALSE) {
                $db->query('ALTER TABLE `'.$table.'_'.$tablename.'` ADD `tableid` SMALLINT(6) UNSIGNED NOT NULL COMMENT \'附表id\';');
            }
            // 创建附表
            $db->query('DROP TABLE IF EXISTS `'.$table.'_'.$tablename.'_data_0`');
            if ($form['sql2']) {
                $db->query(trim(str_replace('{tablename}', $table.'_'.$tablename.'_data_0', $form['sql2'])));
            } else {
                $sql = "
                CREATE TABLE IF NOT EXISTS `".$table.'_'.$tablename."_data_0` (
                  `id` int(10) unsigned NOT NULL,
                  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
                  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者id',
                  UNIQUE KEY `id` (`id`),
                  KEY `cid` (`cid`),
                  KEY `uid` (`uid`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='表单附表';";
                $db->query(trim($sql));
            }
			// 添加字段
			foreach ($form['field'] as $t) {
				unset($t['id']);
				$t['relatedid'] = $id;
				$t['relatedname'] = 'mform-'.$dir;
				$this->db->insert('field', $t);
			}
			$name = 'Form_'.$tablename;
			// 创建管理控制器
			$file = FCPATH.'module/'.$dir.'/controllers/admin/'.$name.'.php';
			if (!is_file($file)) {
				file_put_contents($file, '<?php'.PHP_EOL.PHP_EOL
				.'require WEBPATH.\'dayrui/core/D_Admin_Form.php\';'.PHP_EOL.PHP_EOL
				.'class '.$name.' extends D_Admin_Form {'.PHP_EOL.PHP_EOL
				.'	public function __construct() {'.PHP_EOL
				.'		parent::__construct();'.PHP_EOL
				.'	}'.PHP_EOL
				.'}');
			}
			// 会员控制器
			$file = FCPATH.'module/'.$dir.'/controllers/member/'.$name.'.php';
			if (!is_file($file)) {
				file_put_contents($file, '<?php'.PHP_EOL.PHP_EOL
				.'require FCPATH.\'dayrui/core/D_Member_Form.php\';'.PHP_EOL.PHP_EOL
				.'class '.$name.' extends D_Member_Form {'.PHP_EOL.PHP_EOL
				.'	public function __construct() {'.PHP_EOL
				.'		parent::__construct();'.PHP_EOL
				.'	}'.PHP_EOL
				.'}');
			}
			// 前端发布控制器
			$file = FCPATH.'module/'.$dir.'/controllers/'.$name.'.php';
			if (!is_file($file)) {
				file_put_contents($file, '<?php'.PHP_EOL.PHP_EOL
				.'require FCPATH.\'dayrui/core/D_Home_Form.php\';'.PHP_EOL.PHP_EOL
				.'class '.$name.' extends D_Home_Form {'.PHP_EOL.PHP_EOL
				.'	public function __construct() {'.PHP_EOL
				.'		parent::__construct();'.PHP_EOL
				.'	}'.PHP_EOL
				.'}');
			}
            // 查询后台模块的菜单
            $menu = $this->db
                         ->where('pid<>0')
                         ->where('uri', '')
                         ->where('mark', 'module-'.$dir)
                         ->order_by('displayorder ASC,id ASC')
                         ->get('admin_menu')
                         ->row_array();
            if ($menu) {
                // 将此表单放在模块菜单中
				$this->system_model->add_admin_menu(array(
                    'uri' => $dir.'/admin/'.strtolower($name).'/index',
                    'url' => '',
                    'pid' => $menu['id'],
                    'name' => $form['data']['name'].'管理',
                    'mark' => 'module-'.$dir.'-'.$id,
                    'icon' => 'icon-table',
                    'hidden' => 0,
                    'displayorder' => 0,
                ));
            }
			// 查询表单
			$form = $this->db->where('module', $dir)->get('module_form')->result_array();
			if ($form) {
				$top = $this->db->where('mark', 'm_mod')->where('pid', 0)->get('member_menu')->row_array();
				$left = $this->db->where('mark', 'left-'.$dir)->where('pid', (int)$top['id'])->get('member_menu')->row_array();
				if ($top && $left) {
					// 将此表单放在模块菜单中
					foreach ($form as $f) {
						$this->db->insert('member_menu', array(
							'pid' => $left['id'],
							'url' => '',
							'uri' => $dir.'/form_'.$f['table'].'/index',
							'mark' => 'module-'.$dir,
							'name' => fc_lang('我的%s', $f['name']),
							'icon' => $f['setting']['icon'] ? $f['setting']['icon'] : 'fa fa-th-large',
							'target' => 0,
							'hidden' => 0,
							'displayorder' => 0,
						));
					}
				}
			}
		}
		
		return TRUE;
	}
	
	/**
	 * 字段入库
	 *
	 * @param	intval	$id		模块id
	 * @param	array	$field	字段信息
	 * @param	intval	$ismain	是否主表
	 * @param	intval	$extend	是否是扩展表
	 * @return	bool
	 */
	private function add_field($id, $field, $ismain, $extend = 0) {

		$rname = $extend ? 'extend' : 'module';
		if ($this->db->where('fieldname', $field['fieldname'])->where('relatedid', $id)->where('relatedname', $rname)->count_all_results('field')) {
			return;
		}

		$this->db->insert('field', array(
			'name' => $field['textname'],
			'ismain' => $ismain,
			'setting' => dr_array2string($field['setting']),
			'issystem' => isset($field['issystem']) ? (int)$field['issystem'] : 1,
			'ismember' => isset($field['ismember']) ? (int)$field['ismember'] : 1,
			'disabled' => isset($field['disabled']) ? (int)$field['disabled'] : 0,
			'fieldname' => $field['fieldname'],
			'fieldtype' => $field['fieldtype'],
			'relatedid' => $id,
			'relatedname' => $rname,
			'displayorder' => (int)$field['displayorder'],
		));
	}
	
	/**
	 * 安装到站点
	 *
	 * @param	intval	$id		    模块id
	 * @param	string	$dir	    模块目录
	 * @param	array	$siteid	    站点id
	 * @param	array	$config	    模块配置
     * @param	intval	$nodb       是否自定义数据模块
     * @param	intval	$_siteid    已经安装过的站点id
	 * @return	void
	 */
	public function install($id, $dir, $siteid, $config, $nodb = 0, $_siteid = 0) {
	
		if (!$id || !$dir || !$siteid || !isset($this->site[$siteid])) {
            return NULL;
        }

		if ($dir == 'space') {
			// 安装空间黄页
			if (is_file(FCPATH.'module/'.$dir.'/config/install.sql') && $uninstall = file_get_contents(FCPATH.'module/'.$dir.'/config/install.sql')) {
				$_sql = str_replace(
					array('{dbprefix}'),
					array($this->db->dbprefix),
					$uninstall
				);
				$sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $_sql)));
				foreach($sql_data as $query){
					if (!$query) {
						continue;
					}
					$ret = '';
					$queries = explode('SQL_FINECMS_EOL', trim($query));
					foreach($queries as $query) {
						$ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
					}
					if (!$ret) {
						continue;
					}
					$this->db->query($ret);
				}
				unset($query, $sql_data, $_sql, $queries, $ret);
			}
			return;
		}

		$extend = (int)$config['extend'];
		$install = NULL; // 初始化数据

		// 表前缀部分：站点id_模块目录[_表名称]
		$db = $this->site[$siteid];
		$prefix = $this->db->dbprefix($siteid.'_'.$dir);

        // 非系统表属性时才导入系统表
        if (!$nodb) {
            // 主表
            $sql = '';
            if ($_siteid) {
                // 从站点现存表中获取表结构
                $sql = $db->query("SHOW CREATE TABLE `".$this->db->dbprefix($_siteid.'_'.$dir)."`")->row_array();
                $sql = str_replace(
                    array($sql['Table'], 'CREATE TABLE'),
                    array('{tablename}', 'CREATE TABLE IF NOT EXISTS'),
                    $sql['Create Table']
                );
            }
            if (!$sql) {
                // 从本地配置中获取表结构
                $cfg = require FCPATH.'module/'.$dir.'/config/main.table.php';
                $sql = $cfg['sql'];
            }
            $db->query('DROP TABLE IF EXISTS `'.$prefix.'`');
            $db->query(trim(str_replace('{tablename}', $prefix, $sql)));
            // 更改状态字段长度
            $db->query('ALTER TABLE `'.$prefix.'` CHANGE `status` `status` TINYINT(2) NOT NULL COMMENT "状态";');
            // 附表
            $sql = '';
            if ($_siteid) {
                // 从站点现存表中获取表结构
                $sql = $db->query("SHOW CREATE TABLE `".$this->db->dbprefix($_siteid.'_'.$dir.'_data_0')."`")->row_array();
                $sql = str_replace(
                    array($sql['Table'], 'CREATE TABLE'),
                    array('{tablename}', 'CREATE TABLE IF NOT EXISTS'),
                    $sql['Create Table']
                );
            }
            if (!$sql) {
                // 从本地配置中获取表结构
                $cfg = require FCPATH.'module/'.$dir.'/config/data.table.php';
                $sql = $cfg['sql'];
            }
            $db->query('DROP TABLE IF EXISTS `'.$prefix.'_data_0'.'`');
            $db->query(trim(str_replace('{tablename}', $prefix.'_data_0', $sql)));
            // 创建评论
            $this->load->model('comment_model');
            $this->comment_model->module($dir);
            $this->comment_model->install_sql($_siteid);
            // 扩展表
            if ($extend) {
                // 扩展主表
                $sql = '';
                if ($_siteid) {
                    // 从站点现存表中获取表结构
                    $sql = $db->query("SHOW CREATE TABLE `".$this->db->dbprefix($_siteid.'_'.$dir.'_extend')."`")->row_array();
                    $sql = str_replace(
                        array($sql['Table'], 'CREATE TABLE'),
                        array('{tablename}', 'CREATE TABLE IF NOT EXISTS'),
                        $sql['Create Table']
                    );
                }
                if (!$sql) {
                    // 从本地配置中获取表结构
                    $cfg = require FCPATH.'module/'.$dir.'/config/extend.main.table.php';
                    $sql = $cfg['sql'];
                }
                $db->query('DROP TABLE IF EXISTS `'.$prefix.'_extend'.'`');
                $db->query(trim(str_replace('{tablename}', $prefix.'_extend', $sql)));
                // 创建扩展评论
                $this->comment_model->extend($dir);
                $this->comment_model->install_sql($_siteid);
                // 更改状态字段长度
                $db->query('ALTER TABLE `'.$prefix.'` CHANGE `status` `status` TINYINT(2) NOT NULL COMMENT "状态";');
                // 扩展附表
                $sql = '';
                if ($_siteid) {
                    // 从站点现存表中获取表结构
                    $sql = $db->query("SHOW CREATE TABLE `".$this->db->dbprefix($_siteid.'_'.$dir.'_extend_data_0')."`")->row_array();
                    $sql = str_replace(
                        array($sql['Table'], 'CREATE TABLE'),
                        array('{tablename}', 'CREATE TABLE IF NOT EXISTS'),
                        $sql['Create Table']
                    );
                }
                if (!$sql) {
                    // 从本地配置中获取表结构
                    $cfg = require FCPATH.'module/'.$dir.'/config/extend.data.table.php';
                    $sql = $cfg['sql'];
                }
                $db->query('DROP TABLE IF EXISTS `'.$prefix.'_extend_data_0'.'`');
                $db->query(trim(str_replace('{tablename}', $prefix.'_extend_data_0', $sql)));
            }
            // 系统默认表
            foreach ($this->system_table as $table => $sql) {
				// 不是扩展模块就不执行扩展表
				if (strpos($table, 'extend_') === 0 && !$extend) {
					continue;
				}
                $db->query('DROP TABLE IF EXISTS `'.$prefix.'_'.$table.'`');
                $db->query(trim(str_replace('{tablename}', $prefix.'_'.$table, $sql)));
            }
        } else {
            $install_file = FCPATH.'module/'.$dir.'/config/install.php'; // 自定义安装文件
            if (is_file($install_file)) {
                $is_install = 1;
                require $install_file;
            }
        }

		// 插入初始化数据
		if (is_file(FCPATH.'module/'.$dir.'/config/install.sql')
            && $install = file_get_contents(FCPATH.'module/'.$dir.'/config/install.sql')) {
			$_sql = str_replace(
				array('{tablename}', '{dbprefix}', '{moduleid}', '{moduledir}', '{siteid}'), 
				array($prefix, $this->db->dbprefix, $id, $dir, SITE_ID), 
				$install
			);
			$sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $_sql)));
			foreach($sql_data as $query) {
				if (!$query) {
                    continue;
                }
				$ret = '';
				$queries = explode('SQL_FINECMS_EOL', trim($query));
				foreach($queries as $query) {
					$ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
				}
				if (!$ret) {
                    continue;
                }
                // 如果此模块已经在其他站点中安装就不导入插入语句
                if ($_siteid &&
                    (stripos($ret, 'REPLACE INTO') === 0 || stripos($ret, 'INSERT INTO') === 0)) {
                    continue;
                }
				$db->query($ret);
			}
			unset($query, $sql_data, $_sql, $queries, $ret);
		}

        // 安装表单
        if ($_siteid) {
            // 从站点现存表中获取表结构
            $form = $this->db->where('module', $dir)->get('module_form')->result_array();
            if ($form) {
                foreach ($form as $t) {
                    $table = $this->db->dbprefix($_siteid.'_'.$dir.'_form_'.$t['table']);
                    // 主表
                    $sql = $db->query("SHOW CREATE TABLE `".$table."`")->row_array();
                    $sql = str_replace(
                        array($sql['Table'], 'CREATE TABLE'),
                        array('{tablename}', 'CREATE TABLE IF NOT EXISTS'),
                        $sql['Create Table']
                    );
                    $sql = trim(str_replace('{tablename}', $table, $sql));
                    $db->query($sql);
                    // 附表
                    $sql = $db->query("SHOW CREATE TABLE `".$table."_data_0`")->row_array();
                    $sql = str_replace(
                        array($sql['Table'], 'CREATE TABLE'),
                        array('{tablename}', 'CREATE TABLE IF NOT EXISTS'),
                        $sql['Create Table']
                    );
                    $sql = trim(str_replace('{tablename}', $table.'_data_0', $sql));
                    $db->query($sql);
                }
            }
        } else {
            // 导入本地表单
		    $this->import_form($dir, $siteid);
        }
	}
	
	/**
	 * 从站点中卸载
	 *
	 * @param	intval	$id		模块id
	 * @param	string	$dir	模块目录
	 * @param	array	$siteid	站点id
	 * @param	intval	$delete	是否删除菜单
	 * @return	void
	 */
	public function uninstall($id, $dir, $siteid, $delete = 0) {
	
		if (!$id || !$dir || !$siteid || !isset($this->site[$siteid])) {
            return NULL;
        }
		
		$config = require FCPATH.'module/'.$dir.'/config/module.php'; // 配置信息
		$extend = (int)$config['extend'];

		if ($dir == 'space') {
			// 卸载空间黄页
			$this->db->where('relatedname', 'space')->delete('field');
			$this->db->where('relatedname', 'spacetable')->delete('field');
			// 插入初始化数据
			if (is_file(FCPATH.'module/'.$dir.'/config/uninstall.sql') && $uninstall = file_get_contents(FCPATH.'module/'.$dir.'/config/uninstall.sql')) {
				$_sql = str_replace(
					array('{dbprefix}'),
					array($this->db->dbprefix),
					$uninstall
				);
				$sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $_sql)));
				foreach($sql_data as $query){
					if (!$query) {
						continue;
					}
					$ret = '';
					$queries = explode('SQL_FINECMS_EOL', trim($query));
					foreach($queries as $query) {
						$ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
					}
					if (!$ret) {
						continue;
					}
					$this->db->query($ret);
				}
				unset($query, $sql_data, $_sql, $queries, $ret);
			}
		} else {

			// 数据库
			$db = $this->site[$siteid];

			// 表前缀部分：站点id_模块目录[_表名称]
			$prefix = $this->db->dbprefix($siteid.'_'.$dir);

			// 清空附件
			$this->load->model('attachment_model');
			$this->attachment_model->delete_for_table($prefix, TRUE);

			// 判断是否为系统模块
			$nodb = isset($config['nodb']) && $config['nodb'] ? 1 : 0;
			if (!$nodb) {
				// 主表
				$db->query('DROP TABLE IF EXISTS `'.$prefix.'`');
				// 附表
				for ($i = 0; $i < 100; $i ++) {
					if (!$db->query("SHOW TABLES LIKE '".$prefix.'_data_'.$i."'")->row_array()) {
						break;
					}
					$db->query('DROP TABLE IF EXISTS '.$prefix.'_data_'.$i);
				}
				// 卸载评论
				$this->load->model('comment_model');
				$this->comment_model->module($dir);
				$this->comment_model->uninstall_sql($siteid);
				// 扩展表
				if ($extend) {
					// 卸载扩展评论
					$this->comment_model->extend($dir);
					$this->comment_model->uninstall_sql($siteid);
					// 主表
					$db->query('DROP TABLE IF EXISTS `'.$prefix.'_extend`');
					// 附表
					for ($i = 0; $i < 100; $i ++) {
						if (!$db->query("SHOW TABLES LIKE '".$prefix.'_extend_data_'.$i."'")->row_array()) {
							break;
						}
						$db->query('DROP TABLE IF EXISTS '.$prefix.'_extend_data_'.$i);
					}
				}
				// 表单数据表
				$form = $this->db->where('module', $dir)->get('module_form')->result_array();
				if ($form) {
					foreach ($form as $t) {
						$db->query('DROP TABLE IF EXISTS '.$prefix.'_form_'.$t['id']);
						$this->attachment_model->delete_for_table($prefix.'_form_'.$t['id'], TRUE);
					}
				}
				// 系统默认表
				foreach ($this->system_table as $table => $sql) {
					$db->query('DROP TABLE IF EXISTS `'.$prefix.'_'.$table.'`');
				}
				// 删除栏目字段
				$this->db->where('relatedname', $dir.'-'.$siteid)->delete('field');

			}

			// 当站点数量小于2时删除菜单
			if ($delete < 2) {
				// 删除后台菜单
				$this->db->where('mark', 'module-'.$dir)->delete('admin_menu');
				$this->db->like('mark', 'module-'.$dir.'-%')->delete('admin_menu');
				// 删除会员菜单
				$this->db->where('mark', 'left-'.$dir)->delete('member_menu');
				$this->db->where('mark', 'module-'.$dir)->delete('member_menu');
				$this->db->like('mark', 'module-'.$dir.'-%')->delete('member_menu');
			}

			// 插入初始化数据
			if (is_file(FCPATH.'module/'.$dir.'/config/uninstall.sql') && $uninstall = file_get_contents(FCPATH.'module/'.$dir.'/config/uninstall.sql')) {
				$_sql = str_replace(
					array('{tablename}', '{dbprefix}', '{moduleid}', '{moduledir}', '{siteid}'),
					array($prefix, $this->db->dbprefix, $id, $dir, SITE_ID),
					$uninstall
				);
				$sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $_sql)));
				foreach($sql_data as $query){
					if (!$query) {
						continue;
					}
					$ret = '';
					$queries = explode('SQL_FINECMS_EOL', trim($query));
					foreach($queries as $query) {
						$ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
					}
					if (!$ret) {
						continue;
					}
					$db->query($ret);
				}
				unset($query, $sql_data, $_sql, $queries, $ret);
			}

			// 删除应用相关表
			$app = $this->ci->get_cache('app');
			if ($app) {
				foreach ($app as $adir) {
					if (is_file(FCPATH.'module/'.$adir.'/models/'.$adir.'_model.php')) {
						$this->load->add_package_path(FCPATH.'app/'.$adir.'/');
						$this->load->model($adir.'_model', 'app_model');
						$this->app_model->delete_for_module($dir, $siteid);
						$this->load->remove_package_path(FCPATH.'app/'.$adir.'/');
					}
				}
			}
		}

	}
	
	/**
	 * 清空当前站点的模块数据
	 *
	 * @param	string	$dir	模块目录
	 * @return	void
	 */
	public function clear($dir, $site) {
	
		if (!$dir) {
            return NULL;
        }
		
		$config = require FCPATH.'module/'.$dir.'/config/module.php'; // 配置信息
		$extend = (int)$config['extend'];

        $db = $this->site[$site];
		// 表前缀部分：站点id_模块目录[_表名称]
		$prefix = $this->db->dbprefix($site.'_'.$dir);
		// 主表
		$db->query('TRUNCATE TABLE `'.$prefix.'`');
		// 附表
		for ($i = 0; $i < 100; $i ++) {
			if (!$db->query("SHOW TABLES LIKE '".$prefix.'_data_'.$i."'")->row_array()) {
                break;
            }
			$db->query('TRUNCATE TABLE '.$prefix.'_data_'.$i);
		}
		// 扩展模块
		if ($extend) {
		    // 主表
            $db->query('TRUNCATE TABLE `'.$prefix.'_extend`');
			// 扩展表
			for ($i = 0; $i < 100; $i ++) {
				if (!$db->query("SHOW TABLES LIKE '".$prefix.'_extend_data_'.$i."'")->row_array()) {
                    break;
                }
				$db->query('TRUNCATE TABLE '.$prefix.'_extend_data_'.$i);
			}
		}
		// 系统默认表
		foreach ($this->system_table as $table => $sql) {
			// 不是扩展模块就不执行扩展表
			if (strpos($table, 'extend_') === 0 && !$extend) {
				continue;
			}
			$db->query('TRUNCATE TABLE `'.$prefix.'_'.$table.'`');
		}
		// 删除应用相关表
		$app = $this->ci->get_cache('app');
		if ($app) {
			foreach ($app as $adir) {
				if (is_file(FCPATH.'app/'.$adir.'/models/'.$adir.'_model.php')) {
					$this->load->add_package_path(FCPATH.'app/'.$adir.'/');
					$this->load->model($adir.'_model', 'app_model');
					$this->app_model->delete_for_module($dir, $site);
				}
			}
		}
        // 删除表单数据
        $form = $this->db->where('module', $dir)->get('module_form')->result_array();
        if ($form) {
            foreach ($form as $t) {
                $db->query('TRUNCATE TABLE '.$prefix.'_form_'.$t['id']);
            }
        }
	}
	
	/**
	 * 修改
	 *
	 * @param	array	$_data	老数据
	 * @param	array	$data	新数据
	 * @return	void
	 */
	public function edit($id, $data) {
		$this->db->where('id', $id)->update('module', array(
            'sitemap' => (int)$data['sitemap'],
            'setting' => dr_array2string($data['setting'])
         ));
	}
	
	/**
	 * 删除
	 *
	 * @param	intval	$id
	 * @return	void
	 */
	public function del($id) {
		// 模块信息
		$data = $this->get($id);
		if (!$data) {
            return NULL;
        }
		// 删除模块数据和卸载全部站点
		$this->db->where('id', $id)->delete('module');
		foreach ($data['site'] as $siteid => $url) {
			$this->uninstall($data['id'], $data['dirname'], $siteid);
            $this->db->where('relatedname', $data['dirname'].'-'.$siteid)->delete('field');
		}
		// 删除模块字段
		$this->db->where('relatedname', 'module')->where('relatedid', $id)->delete('field');
		// 删除扩展字段
		$this->db->where('relatedname', 'extend')->where('relatedid', $id)->delete('field');
		// 删除表单字段
		$this->db->where('relatedname', 'mform-'.$data['dirname'])->delete('field');
		// 删除表单
		$this->db->where('module', $data['dirname'])->delete('module_form');
	}
	
	/**
	 * 格式化字段数据
	 *
	 * @param	array	$data	新数据
	 * @return	array
	 */
	private function get_field_value($data) {
		if (!$data) {
            return NULL;
        }
		$data['setting'] = dr_string2array($data['setting']);
		return $data;
	}
	
	/**
	 * 模块缓存
	 *
	 * @param	string	$dirname	模块名称
	 * @param	intval	$update		是否更新数量
	 * @return	NULL
	 */
	public function cache($dirname, $update = 1) {

		if (!$dirname) {
            return NULL;
        }

		$this->load->library('dconfig');
        // 加载站点域名配置文件
		$site_domain = require WEBPATH.'config/domain.php';

		if ($dirname == 'share') {
			// 共享模块

			// 按站点生成缓存
			foreach ($this->site_info as $siteid => $t) {

				$cache = array(
					'share' => 1,
					'field' => array(
						'thumb' => '',
						'title' => '',
						'keywords' => '',
						'description' => '',
					),
					'dirname' => 'share',
				);

				// 模块的栏目分类
				$cdir = $dirname;
				//$share = array();
				$category = $this->site[$siteid]->order_by('displayorder ASC, id ASC')->get($siteid.'_'.$cdir.'_category')->result_array();
				if ($category) {
					$CAT = $CAT_DIR = $fenzhan = $level = array();
					if (function_exists('dr_fenzhan_data')) {
						// 查询分站数据，以便生成url
						$fenzhan = dr_fenzhan_data($siteid);
					}
					foreach ($category as $c) {
						if ($update == 1) {
							if (!$c['child'] || $c['pcatpost']) {
								$c['total'] = $this->site[$siteid]->where('status', 9)->where('catid', $c['id'])->count_all_results($siteid.'_'.$cdir.'_index');
							} else {
								$c['total'] = 0;
							}
						} else {
							$c['total'] = $this->ci->get_cache('module-'.$siteid.'-'.$cdir, 'category', $c['id'], 'total');
						}
						if ($c['domain']) {
							$site_domain[$c['domain']] = $siteid;
						}
						$pid = explode(',', $c['pids']);
						$level[] = substr_count($c['pids'], ',');
						$c['mid'] = $c['tid'] == 1 ? $c['mid'] : '';
						$c['topid'] = isset($pid[1]) ? $pid[1] : $c['id'];
						$c['catids'] = explode(',', $c['childids']);
						$c['domain'] = $c['domain'] ? dr_http_prefix($c['domain'].'/') : '';
						$c['setting'] = dr_string2array($c['setting']);
						$c['permission'] = $c['child'] && !$c['pcatpost'] ? '' : dr_string2array($c['permission']);
						$c['fenzhan'][0] = $c['url'] = isset($c['setting']['linkurl']) && $c['setting']['linkurl'] ? $c['setting']['linkurl'] : dr_category_url($cache, $c, 0, $siteid);
						// 按分站生成url
						if ($fenzhan) {
							foreach ($fenzhan as $fz) {
								$c['fenzhan'][$fz['id']] = isset($c['setting']['linkurl']) && $c['setting']['linkurl'] ? $c['setting']['linkurl'] : dr_category_url($cache, $c, 0, $siteid, $fz['cname']);
								$c['fenzhan'][$fz['cname']] = $c['fenzhan'][$fz['id']];
							}
						}
						// 删除过期的部分
						unset($c['setting']['urlmode']);
						unset($c['setting']['url']);
						$CAT[$c['id']] = $c;
						$CAT_DIR[$c['dirname']] = $c['id'];
						/*
						if ($c['tid'] == 1 && $c['mid']) {
							$share['id'][$c['id']] = $c['mid'];
							$share['dir'][$c['dirname']] = $c['mid'];
						}*/
					}
					// 更新父栏目数量
					if ($update == 1) {
						foreach ($category as $c) {
							if ($c['child']) {
								$arr = explode(',', $c['childids']);
								$CAT[$c['id']]['total'] = 0;
								foreach ($arr as $i) {
									$CAT[$c['id']]['total']+= $CAT[$i]['total'];
								}
							}
						}
					}
					// 栏目自定义字段，把父级栏目的字段合并至当前栏目
					$field = $this->db
								->where('disabled', 0)
								->where('relatedname', $dirname.'-'.$siteid)
								->order_by('displayorder ASC, id ASC')
								->get('field')->result_array();
					if ($field) {
						foreach ($field as $f) {
							if (isset($CAT[$f['relatedid']]['childids'])
								&& $CAT[$f['relatedid']]['childids']) {
								// 将该字段同时归类至其子栏目
								$child = explode(',', $CAT[$f['relatedid']]['childids']);
								foreach ($child as $catid) {
									if ($CAT[$catid]) {
										$CAT[$catid]['field'][$f['fieldname']] = $this->get_field_value($f);
									}
								}
							}
						}
					}
					$cache['category'] = $CAT;
					$cache['category_dir'] = $CAT_DIR;
					$cache['category_field'] = $field ? 1 : 0;
					$cache['category_level'] = $level ? max($level) : 0;
				} else {
					$cache['category'] = array();
					$cache['category_dir'] = array();
					$cache['category_field'] = $cache['category_level'] = 0;
				}
				$this->dcache->set('module-'.$siteid.'-'.$dirname, $cache);
			}
			// 写入共享栏目配置文件
			//$this->dconfig->file(WEBPATH.'config/route/category.php')->note('共享栏目路由配置')->space(32)->to_require($share);
		} elseif (is_dir(FCPATH.'module/'.$dirname.'/')) {
			// 独立模块
			$data = $this->db
					->where('disabled', 0)
					->where('dirname', $dirname)
					->get('module')->row_array();
			if (!$data) {
				return NULL;
			}

			$config = require FCPATH.'module/'.$dirname.'/config/module.php'; // 配置信息
			$data['site'] = dr_string2array($data['site']);
			$config['nodb'] = $dirname == 'weixin' ? 1 : intval($config['nodb']); // 将微信强制列入非系统数据表类型
			$data['setting'] = dr_string2array($data['setting']);

			// 模块表单数据
			$form = array();
			$temp = $this->db->where('disabled', 0)->order_by('id ASC')->get('module_form')->result_array();
			if ($temp) {
				foreach ($temp as $t) {
					$t['field'] = array();
					// 模块表单的自定义字段
					$field = $this->db
								->where('disabled', 0)
								->where('relatedid', $t['id'])
								->where('relatedname', 'mform-'.$data['dirname'])
								->order_by('displayorder ASC, id ASC')
								->get('field')
								->result_array();
					if ($field) {
						foreach ($field as $f) {
							$t['field'][$f['fieldname']] = $this->get_field_value($f);
						}
					}
					$t['setting'] = dr_string2array($t['setting']);
					$t['permission'] = dr_string2array($t['permission']);
					$form[$t['module']][$t['table']] = $t;
				}
			}

			// 按站点生成缓存
			foreach ($this->site_info as $siteid => $t) {
				$cache = $data;
                $this->dcache->set('module-'.$siteid.'-'.$dirname, array());
				if (isset($data['site'][$siteid]['use']) && $data['site'][$siteid]['use']) {
					// 模块域名
					$domain = $data['site'][$siteid]['domain'];
					if ($domain) {
						$site_domain[$domain] = $siteid;
					}
					// 将站点保存至域名配置文件
					$cache['html'] = $data['site'][$siteid]['html'];
					$cache['theme'] = $data['site'][$siteid]['theme'];
					$cache['domain'] = $domain ? dr_http_prefix($domain.'/') : '';
					$cache['template'] = $data['site'][$siteid]['template'];
					// 模块的URL地址
					$cache['url'] = dr_module_url($cache, $siteid);
					// 模块的自定义字段
					$field = $this->db
								->where('disabled', 0)
								->where('relatedid', $data['id'])
								->where('relatedname', 'module')
								->order_by('displayorder ASC, id ASC')
								->get('field')->result_array();
					if ($field) {
						foreach ($field as $f) {
							$cache['field'][$f['fieldname']] = $this->get_field_value($f);
						}
					} else {
						$cache['field'] = array();
					}
					// 模块扩展的自定义字段
					if ($data['extend']) {
						$field = $this->db
									->where('disabled', 0)
									->where('relatedid', $data['id'])
									->where('relatedname', 'extend')
									->order_by('displayorder ASC, id ASC')
									->get('field')->result_array();
						$cache['extend'] = array();
						if ($field) {
							foreach ($field as $f) {
								$cache['extend'][$f['fieldname']] = $this->get_field_value($f);
							}
						}
					} else {
						$cache['extend'] = 0;
					}
					// 模块表单归类
					$cache['form'] = isset($form[$dirname]) ? $form[$dirname] : array();
					// 模块表创建统计字段
					if ($cache['form']) {
						foreach ($cache['form'] as $fm) {
							$this->system_model->create_form_total_field($siteid, $dirname, $fm['table'], 1);
						}
					}
					// 系统模块格式
					if ($config['nodb'] == 0) {
						// 模块的栏目分类
						// 判断关联栏目
                        if ($data['share']) {
                            $cdir = 'share';
                            $category = $this->db->where('mid', $dirname)->order_by('displayorder ASC, id ASC')->get($siteid.'_share_category')->result_array();
                        } else {
                            $cdir = ($config['category'] ? $config['category'] : $dirname);
                            $category = $this->db->order_by('displayorder ASC, id ASC')->get($siteid.'_'.$cdir.'_category')->result_array();
                        }
						if ($category) {
							$CAT = $CAT_DIR = $fenzhan = $level = array();
							if (function_exists('dr_fenzhan_data')) {
								// 查询分站数据，以便生成url
								$fenzhan = dr_fenzhan_data($siteid);
							}
							foreach ($category as $c) {
								$c['total'] = $update == 1 ? ((!$c['child'] || $data['setting']['pcatpost']) ? $this->site[$siteid]->where('status', 9)->where('catid', $c['id'])->count_all_results($siteid.'_'.$cdir.'_index') : 0) : $this->ci->get_cache('module-'.$siteid.'-'.$cdir, 'category', $c['id'], 'total');
								$pid = explode(',', $c['pids']);
								$level[] = substr_count($c['pids'], ',');
								$c['mid'] = isset($c['mid']) ? $c['mid'] : $cache['dirname'];
								$c['topid'] = isset($pid[1]) ? $pid[1] : $c['id'];
								$c['domain'] = isset($c['domain']) ? $c['domain'] : $cache['domain'];
								$c['catids'] = explode(',', $c['childids']);
								$c['setting'] = dr_string2array($c['setting']);
								$c['pcatpost'] = intval($cache['setting']['pcatpost']);
								$c['setting']['html'] = $cdir == 'share' ? intval($c['setting']['html']) : $cache['html'];
								$c['setting']['urlrule'] = intval($c['setting']['urlrule'] ? $c['setting']['urlrule'] : $cache['site'][$siteid]['urlrule']);
								$c['permission'] = $c['child'] && !$data['setting']['pcatpost'] ? '' : dr_string2array($c['permission']);
								$c['fenzhan'][0] = $c['url'] = isset($c['setting']['linkurl']) && $c['setting']['linkurl'] ? $c['setting']['linkurl'] : dr_category_url($cache, $c, 0, $siteid);
								// 按分站生成url
								if ($fenzhan) {
									foreach ($fenzhan as $fz) {
										$c['fenzhan'][$fz['id']] = isset($c['setting']['linkurl']) && $c['setting']['linkurl'] ? $c['setting']['linkurl'] : dr_category_url($cache, $c, 0, $siteid, $fz['cname']);
										$c['fenzhan'][$fz['cname']] = $c['fenzhan'][$fz['id']];
									}
								}
								// 删除过期的部分
								unset($c['setting']['urlmode']);
								unset($c['setting']['url']);
								$CAT[$c['id']] = $c;
								$CAT_DIR[$c['dirname']] = $c['id'];

							}
							// 更新父栏目数量
							if ($update == 1) {
								foreach ($category as $c) {
									if ($c['child']) {
										$arr = explode(',', $c['childids']);
										$CAT[$c['id']]['total'] = 0;
										foreach ($arr as $i) {
											$CAT[$c['id']]['total']+= $CAT[$i]['total'];
										}
									}
								}
							}
							// 栏目自定义字段，把父级栏目的字段合并至当前栏目
							$field = $this->db
										->where('disabled', 0)
										->where('relatedname', $dirname.'-'.$siteid)
										->order_by('displayorder ASC, id ASC')
										->get('field')->result_array();
							if ($field) {
								foreach ($field as $f) {
									if (isset($CAT[$f['relatedid']]['childids'])
										&& $CAT[$f['relatedid']]['childids']) {
										// 将该字段同时归类至其子栏目
										$child = explode(',', $CAT[$f['relatedid']]['childids']);
										foreach ($child as $catid) {
											$CAT[$catid] && $CAT[$catid]['field'][$f['fieldname']] = $this->get_field_value($f);
										}
									}
								}
							}
							$cache['category'] = $CAT;
							$cache['category_dir'] = $CAT_DIR;
							$cache['category_field'] = $field ? 1 : 0;
							$cache['category_level'] = $level ? max($level) : 0;
						} else {
							$cache['category'] = array();
							$cache['category_dir'] = array();
							$cache['category_field'] = $cache['category_level'] = 0;
						}
						$cache['is_system'] = 1;
						// 兼容性处理，安装评论功能
						if (SYS_UPDATE && !$this->site[$siteid]->query("SHOW TABLES LIKE '".$this->db->dbprefix.$siteid.'_'.$dirname."_comment'")->row_array()) {
							$this->load->model('comment_model');
							$this->comment_model->module($dirname);
							$this->comment_model->install_sql($siteid);
							if ($cache['extend']
								&& !$this->site[$siteid]->query("SHOW TABLES LIKE '".$this->db->dbprefix.$siteid.'_'.$dirname."_extend_comment'")->row_array()) {
								$this->comment_model->extend($dirname);
								$this->comment_model->install_sql($siteid);
							}
						}
					} else {
						$cache['is_system'] = 0;
					}
					// 模块名称
					$name = $this->db
								->select('name,icon')
								->where('pid', 0)
								->where('mark', 'module-'.$dirname)
								->get('admin_menu')
								->row_array();
					$cache['name'] = $name['name'] ? $name['name'] : $config['name'];
					$cache['icon'] = $name['icon'] ? $name['icon'] : 'fa fa-square';
					$this->dcache->set('module-'.$siteid.'-'.$dirname, $cache);
				}
			}

		} else {
			return NULL;
		}


		$this->dconfig->file(WEBPATH.'config/domain.php')->note('站点域名文件')->space(32)->to_require_one($site_domain);
	}
}