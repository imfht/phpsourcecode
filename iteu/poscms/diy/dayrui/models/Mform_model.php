<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Mform_model extends CI_Model {
	
	/**
	 * 模块表单模型类
	 */
    public function __construct() {
        parent::__construct();
    }
	
	/**
	 * 添加表单
	 * 
	 * @param	array	$data
	 * @return	string|TRUE
	 */
	public function add($dir, $data) {

        if (!$data['name'] || !$data['table']) {
            return fc_lang('名称或者表名称不能为空');
		} elseif (!preg_match('/^[a-z0-9]+$/i', $data['table'])) {
			return fc_lang('表名称格式不正确');
        } elseif ($this->db->where('module', $dir)->where('table', $data['table'])->count_all_results('module_form')) {
            return fc_lang('表名称已经存在');
        }

        // 插入表单数据
		$this->db->insert('module_form', array(
			'name' => $data['name'],
            'table' => $data['table'],
            'module' => $dir,
			'setting' => dr_array2string($data['setting']),
			'disabled' => 0,
			'permission' => dr_array2string($data['permission']),
		));

        // 执行成功的操作
		if ($id = $this->db->insert_id()) {

            // 表单控制器名称
			$name = 'Form_'.$data['table'];

			// 管理控制器
			$file = FCPATH.'module/'.$dir.'/controllers/admin/'.$name.'.php';
			if (!@file_put_contents($file, '<?php'.PHP_EOL.PHP_EOL
			.'require FCPATH.\'dayrui/core/D_Admin_Form.php\';'.PHP_EOL.PHP_EOL
			.'class '.$name.' extends D_Admin_Form {'.PHP_EOL.PHP_EOL
			.'	public function __construct() {'.PHP_EOL
			.'		parent::__construct();'.PHP_EOL
			.'	}'.PHP_EOL
			.'}')) {
				$this->db->where('id', $id)->delete('module_form');
				return fc_lang('目录(%s)没有写入权限', FCPATH.'module/'.$dir.'/controllers/admin/');
			}

			// 会员控制器
			$file = FCPATH.'module/'.$dir.'/controllers/member/'.$name.'.php';
			if (!@file_put_contents($file, '<?php'.PHP_EOL.PHP_EOL
			.'require FCPATH.\'dayrui/core/D_Member_Form.php\';'.PHP_EOL.PHP_EOL
			.'class '.$name.' extends D_Member_Form {'.PHP_EOL.PHP_EOL
			.'	public function __construct() {'.PHP_EOL
			.'		parent::__construct();'.PHP_EOL
			.'	}'.PHP_EOL
			.'}')) {
				$this->db->where('id', $id)->delete('module_form');
				return fc_lang('目录(%s)没有写入权限', FCPATH.'module/'.$dir.'/controllers/member/');
			}

			// 前端发布控制器
			$file = FCPATH.'module/'.$dir.'/controllers/'.$name.'.php';
			if (!@file_put_contents($file, '<?php'.PHP_EOL.PHP_EOL
			.'require FCPATH.\'dayrui/core/D_Home_Form.php\';'.PHP_EOL.PHP_EOL
			.'class '.$name.' extends D_Home_Form {'.PHP_EOL.PHP_EOL
			.'	public function __construct() {'.PHP_EOL
			.'		parent::__construct();'.PHP_EOL
			.'	}'.PHP_EOL
			.'}')) {
				$this->db->where('id', $id)->delete('module_form');
				return fc_lang('目录(%s)没有写入权限', APPPATH.'controllers/');
			}

            // 按站点更新模块表数据
            $sql1 = "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者id',
			  `author` varchar(50) NOT NULL COMMENT '作者名称',
			  `inputip` varchar(30) DEFAULT NULL COMMENT '录入者ip',
			  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
			  `title` varchar(255) DEFAULT NULL COMMENT '内容主题',
			  `url` varchar(255) DEFAULT NULL COMMENT '内容地址',
			  `subject` varchar(255) DEFAULT NULL COMMENT '表单主题',
	          `tableid` smallint(5) unsigned NOT NULL COMMENT '附表id',
			  PRIMARY KEY `id` (`id`),
			  KEY `cid` (`cid`),
			  KEY `uid` (`uid`),
			  KEY `author` (`author`),
			  KEY `inputtime` (`inputtime`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='".$data['name']."表单数据表';";

            $sql2 = "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL,
			  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者id',
			  UNIQUE KEY `id` (`id`),
			  KEY `cid` (`cid`),
			  KEY `uid` (`uid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='".$data['name']."表单附表';";

            // 获取所有站点的模块
            $module = $this->ci->get_cache('module');
            foreach ($module as $sid => $mod) {
                // 更新站点模块
                if (!in_array($dir, $mod)) {
                    continue;
                }
                // 主表
                $table = $this->db->dbprefix($sid.'_'.$dir.'_form_'.$data['table']);
                $this->db->query("DROP TABLE IF EXISTS `".$table."`");
                $this->db->query(str_replace('{tablename}', $table, $sql1));
                // 附表
                $this->db->query("DROP TABLE IF EXISTS `".$table."_data_0`");
                $this->db->query(str_replace('{tablename}', $table.'_data_0', $sql2));
                // 模块表创建统计字段
                $this->system_model->create_form_total_field($sid, $dir, $data['table'], 1);
            }

            // 字段入库
			$this->db->insert('field', array(
				'name' => '主题',
				'fieldname' => 'subject',
				'fieldtype' => 'Text',
				'relatedid' => $id,
				'relatedname' => 'mform-'.$this->dir,
				'isedit' => 1,
				'ismain' => 1,
				'ismember' => 1,
				'issystem' => 1,
				'issearch' => 1,
				'disabled' => 0,
				'setting' => dr_array2string(array(
					'option' => array(
						'width' => 300, // 表单宽度
						'fieldtype' => 'VARCHAR', // 字段类型
						'fieldlength' => '255' // 字段长度
					),
					'validate' => array(
						'xss' => 1, // xss过滤
						'required' => 1, // 表示必填
					)
				)),
				'displayorder' => 0,
			));

			// 查询后台模块的菜单
			$menu = $this->db
						 ->where('pid<>0')
						 ->where('uri', '')
						 ->where('mark', 'module-'.$dir)
						 ->order_by('displayorder ASC,id ASC')
						 ->get('admin_menu')->row_array();
            if ($menu) {
                // 将此表单放在模块菜单中
				$this->system_model->add_admin_menu(array(
                    'uri' => $dir.'/admin/'.strtolower($name).'/index',
                    'url' => '',
                    'pid' => $menu['id'],
                    'name' => $data['name'].'管理',
                    'mark' => 'module-'.$dir.'-'.$id,
					'icon' => $data['setting']['icon'] ? $data['setting']['icon'] : 'fa fa-th-large',
                    'hidden' => 0,
                    'displayorder' => 0,
                ));
            }
			$top = $this->db->where('mark', 'm_mod')->where('pid', 0)->get('member_menu')->row_array();
			$left = $this->db->where('mark', 'left-'.$dir)->where('pid', (int)$top['id'])->get('member_menu')->row_array();
			if ($top && $left) {
				// 将此表单放在模块菜单中
				$this->db->insert('member_menu', array(
					'pid' => $left['id'],
					'url' => '',
					'uri' => $dir.'/'.strtolower($name).'/index',
					'mark' => 'module-'.$dir,
					'name' => fc_lang('我的%s', $data['name']),
					'icon' => $data['setting']['icon'] ? $data['setting']['icon'] : 'fa fa-th-large',
					'target' => 0,
					'hidden' => 0,
					'displayorder' => 0,
				));
			}
		}

		return FALSE;
	}
	
	/**
	 * 删除
	 * 
	 * @param	intval	$id
	 * @param	intval	$sid
	 */
	public function del($id, $dir) {

		if (!$id || !$dir) {
            return NULL;
        }

        $data = $this->db->where('id', $id)->get('module_form')->row_array();
        if (!$data) {
            return NULL;
        }

        $tablename = $data['table'];
        $this->load->model('attachment_model');

        // 删除字段
		$this->db->where('relatedid', (int)$id)->where('relatedname', 'mform-'.$dir)->delete('field');

        // 删除菜单
        $this->db->where('mark', 'module-'.$dir.'-'.$id)->delete('admin_menu');

        // 按站点来删除表
        $module = $this->ci->get_cache('module');

        // 获取所有站点的模块
        foreach ($module as $sid => $mod) {
            // 更新站点模块
            if (!in_array($dir, $mod)) {
                continue;
            }
            $table = $this->db->dbprefix($sid.'_'.$dir.'_form_'.$tablename);
            // 删除表单表
            $this->db->query('DROP TABLE IF EXISTS `'.$table.'`');
            // 删除附表
            for ($i = 0; $i < 100; $i ++) {
                if (!$this->db->query("SHOW TABLES LIKE '".$table.'_data_'.$i."'")->row_array()) {
                    break;
                }
                $this->db->query('DROP TABLE IF EXISTS '.$table.'_data_'.$i);
            }
            // 删除模块表统计字段
            $this->system_model->create_form_total_field($sid, $dir, $data['table'], -1);
            // 删除附件
            $this->attachment_model->delete_for_table($table, TRUE);
            $this->attachment_model->delete_for_table($table.'_'.$id, TRUE);
        }

		// 删除数据记录
		$this->db->where('id', $id)->delete('module_form');

        // 删除文件
		@unlink(FCPATH.'module/'.$this->dir.'/controllers/Form_'.$tablename.'.php');
		@unlink(FCPATH.'module/'.$this->dir.'/controllers/admin/Form_'.$tablename.'.php');
		@unlink(FCPATH.'module/'.$this->dir.'/controllers/member/Form_'.$tablename.'.php');
		
		return NULL;
	}
	
	//-------------------------------------------------------//
	
	
	/**
	 * 获取表单内容
	 *
	 * @param	intval	$id
	 * @return	intavl
	 */
	public function get($id, $fid, $dir = '') {
		
		if (!$fid || !$id) {
            return NULL;
        }

        $dir = $dir ? $dir : APP_DIR;
		$table = SITE_ID.'_'.$dir.'_form_'.$fid;
		$data = $this->db->where('id', $id)->get($table)->row_array();
		$data2 = $this->db->where('id', $id)->get($table.'_data_'.intval($data['tableid']))->row_array();

        return $data2 ? array_merge($data, $data2) : $data;
	}
}