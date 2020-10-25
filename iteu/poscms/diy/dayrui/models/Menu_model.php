<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class Menu_model extends CI_Model{

	private $ids;

	/**
	 * 菜单模型类
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 顶级菜单id
	 *
	 * @return	array
	 */
	public function get_top_id() {

		$_data = $this->db->select('id')->where('pid=0')->order_by('id ASC')->get('admin_menu')->result_array();
		if (!$_data) {
			return NULL;
		}

		$data = array();
		foreach ($_data as $t) {
			$data[] = $t['id'];
		}

		return $data;
	}

	/**
	 * 分组菜单id
	 *
	 * @return	array
	 */
	public function get_left_id() {

		$_data = $this->db->select('id')->where_in('pid', $this->get_top_id())->order_by('id ASC')->get('admin_menu')->result_array();
		if (!$_data) {
			return NULL;
		}

		$data = array();
		foreach ($_data as $t) {
			$data[] = $t['id'];
		}

		return $data;
	}

	/**
	 * 添加菜单
	 *
	 * @param	array	$data	添加数据
	 * @return	void
	 */
	public function add($data) {

		if (!$data) {
			return NULL;
		}

		$uri = '/';
		$data['dir'] && $uri.= $data['dir'].'/';
		$data['directory'] && $uri.= $data['directory'].'/';
		$data['class'] && $uri.= $data['class'].'/';
		$data['method'] && $uri.= $data['method'].'/';
		$data['param'] && $uri.= $data['param'].'/';

		$insert	= array(
			'uri' => trim($uri, '/'),
			'url' => $data['url'],
			'pid' => $data['pid'],
			'name' => $data['name'],
			'icon' => $data['icon'],
			'hidden' => (int)$data['hidden'],
			'displayorder' => 0,
		);

		$this->db->insert('admin_menu', $insert);
		$insert['id'] = $this->db->insert_id();
		$this->cache();

		return $insert;
	}

	/**
	 * 修改菜单
	 *
	 * @param	array	$_data	旧数据
	 * @param	array	$data	数据
	 * @return	void
	 */
	public function edit($_data, $data) {

		if (!$data || !$_data) {
			return NULL;
		}

		$uri = '/';
		$data['dir'] && $uri.= $data['dir'].'/';
		$data['directory'] && $uri.= $data['directory'].'/';
		$data['class'] && $uri.= $data['class'].'/';
		$data['method'] && $uri.= $data['method'].'/';
		$data['param'] && $uri.= $data['param'].'/';

		$this->db->where('id', $_data['id'])->update('admin_menu', array(
			'uri' => trim($uri, '/'),
			'url' => $data['url'],
			'pid' => $data['pid'],
			'name' => $data['name'],
			'icon' => $data['icon'],
			'hidden' => (int)$data['hidden'],
		));

		$this->cache();

		return $_data['id'];
	}

	/**
	 * 父级菜单选择
	 *
	 * @param	intval	$level	级别
	 * @param	intval	$id		选中项id
	 * @param	intval	$name	select部分
	 * @return	string
	 */
	public function parent_select($level, $id = NULL, $name = NULL) {

		$select = $name ? $name : '<select name="data[pid]">';

		switch ($level) {
			case 0: // 顶级菜单
				$select.= '<option value="0">'.fc_lang('顶级菜单').'</option>';
				break;
			case 1: // 分组菜单
				$topdata = $this->db->select('id,name')->where('pid=0')->get('admin_menu')->result_array();
				foreach ($topdata as $t) {
					$select.= '<option value="'.$t['id'].'"'.($id == $t['id'] ? ' selected' : '').'>'.$t['name'].'</option>';
				}
				break;
			case 2: // 链接菜单
				$topdata = $this->db->select('id,name')->where('pid=0')->get('admin_menu')->result_array();
				foreach ($topdata as $t) {
					$select.= '<optgroup label="'.$t['name'].'">';
					$linkdata = $this->db->select('id,name')->where('pid='.$t['id'])->get('admin_menu')->result_array();
					foreach ($linkdata as $c) {
						$select.= '<option value="'.$c['id'].'"'.($id == $c['id'] ? ' selected' : '').'>'.$c['name'].'</option>';
					}
					$select.= '</optgroup>';
				}
				break;
		}

		$select.= '</select>';

		return $select;
	}

	/**
	 * 更新缓存
	 *
	 * @return	array
	 */
	public function cache($is_link = 0) {

		$this->ci->clear_cache('menu');
		$this->ci->clear_cache('link');

		$list = $link = array();
		$data = $this->db->where('hidden', 0)->order_by('displayorder ASC,id ASC')->get('admin_menu')->result_array();
		if ($data) {
			foreach ($data as $t) {
				if ($t['pid'] == 0) {
					$list[$t['id']] = $t;
					foreach ($data as $m) {
						if ($m['pid'] == $t['id']) {
							$list[$t['id']]['left'][$m['id']] = $m;
							foreach ($data as $n) {
								$n['pid'] == $m['id'] && $list[$t['id']]['left'][$m['id']]['link'][$n['id']] = $n;
							}
						}
					}
				}
				strlen($t['uri']) > 5 && $link[$t['uri']] = 1;
			}
			$this->ci->dcache->set('menu', $list);
			$this->ci->dcache->set('link', $link);
		} else {
			$this->ci->dcache->delete('menu');
			$this->ci->dcache->delete('link');
		}

		return $is_link ? $link : $list;
	}

	/**
	 * 初始化菜单
	 *
	 * @return	array
	 */
	public function init() {
		// 清空菜单
		$this->db->query('TRUNCATE `'.$this->db->dbprefix('admin_menu').'`');
		// 导入初始化菜单数据
		$this->ci->sql_query(str_replace(
			'{dbprefix}',
			$this->db->dbprefix,
			file_get_contents(WEBPATH.'cache/install/admin_menu.sql')
		));
		// 按模块安装菜单
		$module = $this->db->get('module')->result_array();
		if (MEMBER_OPEN_SPACE) {
			$module[] = array(
				'id' => 0,
				'dirname' => 'space',
				'share' => 0,
			);
		}
		if ($module) {
			foreach ($module as $m) {
				$this->init_module($m);
			}
		}
		// 按应用安装菜单
		$app = $this->db->get('application')->result_array();
		if ($app) {
			foreach ($app as $a) {
				$dir = $a['dirname'];
				if (is_file(FCPATH.'app/'.$dir.'/config/menu.php')) {
					$menu = require FCPATH.'app/'.$dir.'/config/menu.php';
					$this->system_model->add_app_menu($menu, $dir, $a['id']);
				}
			}
		}
		// 安装空间模型
		if (MEMBER_OPEN_SPACE) {
			$space = $this->db->get('space_model')->result_array();
			if ($space) {
				$left = $this->db->where('mark', 'space-model')->get('admin_menu')->row_array();
				if (!$left) {
					$this->db->insert('admin_menu', array(
						'pid' => 5,
						'uri' => '',
						'url' => '',
						'mark' => 'space-model',
						'name' => '内容管理',
						'icon' => 'icon-table',
						'hidden' => 0,
						'displayorder' => 0,
					));
					$leftid = $this->db->insert_id();
				} else {
					$leftid = intval($left['id']);
				}
				foreach ($space as $t) {
					$id = $t['id'];
					$uri = 'space/content/index/mid/' . $id;
					if (!$this->db->where('mark', 'space-' . $id)->count_all_results('admin_menu')) {
						$this->db->insert('admin_menu', array(
							'pid' => $leftid,
							'uri' => $uri,
							'url' => '',
							'mark' => 'space-' . $id,
							'name' => $t['name'] . '管理',
							'icon' => 'icon-table',
							'hidden' => 0,
							'displayorder' => $id + 5,
						));
					}
				}
			}
		}
		// 按分支系统安装
		if ($this->ci->branch) {
			foreach ($this->ci->branch as $dir) {
				$path = FCPATH.'branch/'.$dir.'/';
				if (!is_dir($path)) {
					continue;
				}
				// 运行菜单
				is_file($path.'admin_menu.sql') && $sql = file_get_contents($path.'admin_menu.sql') && $this->_query(str_replace('{dbprefix}', $this->db->dbprefix, $sql));
				// 安装菜单
				$menu = require $path.'menu.php';
				if ($menu['admin'] && $menu['admin']['menu']) {
					// 插入后台的顶级菜单
					$this->system_model->add_admin_menu(array(
						'uri' => '',
						'pid' => 0,
						'mark' => 'branch-'.$dir,
						'name' => $menu['admin']['name'],
						'icon' => $menu['admin']['icon'] ? $menu['admin']['icon'] : dr_get_icon_m($dir),
						'hidden' => 0,
						'displayorder' => 0,
					));
					$topid = $this->db->insert_id();
					foreach ($menu['admin']['menu'] as $left) { // 分组菜单名称
						$this->system_model->add_admin_menu(array(
							'uri' => '',
							'pid' => $topid,
							'mark' => 'branch-'.$dir,
							'name' => $left['name'],
							'icon' => $left['icon'] ? $left['icon'] : dr_get_icon_left($left['name']),
							'hidden' => 0,
							'displayorder' => 0,
						));
						$leftid = $this->db->insert_id();
						foreach ($left['menu'] as $link) { // 链接菜单
							$this->system_model->add_admin_menu(array(
								'pid' => $leftid,
								'uri' => 'admin/'.$link['uri'],
								'mark' => 'branch-'.$dir,
								'name' => $link['name'],
								'icon' => $link['icon'] ? $link['icon'] : dr_get_icon($link['uri']),
								'hidden' => 0,
								'displayorder' => 0,
							));
						}
					}
				}
			}
		}
	}

	// 获取自己id和子id
	private function _get_id($id) {

		if (!$id) {
			return NULL;
		}

		$this->ids[$id] = $id;

		$data = $this->db->select('id')->where('pid', $id)->get('admin_menu')->result_array();
		if (!$data) {
			return NULL;
		}

		foreach ($data as $t) {
			$this->ids[$t['id']] = $t['id'];
			$this->_get_id($t['id']);
		}
	}

	// 删除菜单
	public function delete($ids) {

		$this->ids = array();

		if (is_array($ids)) {
			foreach ($ids as $id) {
				$this->_get_id($id);
			}
		} else {
			$this->_get_id($ids);
		}

		$this->ids && $this->db->where_in('id', $this->ids)->delete('admin_menu');

	}

	// 执行sql
	private function _query($sql) {

		if (!$sql) {
			return NULL;
		}

		$sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $sql)));

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
	}

	// 安装模块菜单
	public function init_module($m) {

		$id = $m['id'];
		$dir = $m['dirname'];
		// 菜单
		if (is_file(FCPATH.'module/'.$dir.'/config/menu.php')) {
			$config = require FCPATH.'module/'.$dir.'/config/module.php';
			$name = $config['name']; // 顶部菜单名称
			$menu = require FCPATH.'module/'.$dir.'/config/menu.php';
			if ($m['share']) {
				// 共享栏目时,
				$top = $this->db->where('mark', 'share')->where('pid', 0)->get('admin_menu')->row_array();
				if ($top) {
					$topid = intval($top['id']);
					// 分组菜单名称
					$this->db->insert('admin_menu', array(
						'uri' => '',
						'pid' => $topid,
						'mark' => $menu['mark'] ? $menu['mark'] : 'module-'.$dir,
						'name' => $name.'管理',
						'icon' => $menu['icon'] ? $menu['icon'] : dr_get_icon_m($dir),
						'hidden' => 0,
						'displayorder' => 0,
					));
					$leftid = $this->db->insert_id();
					// 循环链接菜单归类到当前分组
					foreach ($menu['admin'] as $left) {
						foreach ($left['menu'] as $link) { // 链接菜单
							if (in_array($link['uri'], array(
									'admin/category/index',
									'admin/page/index',
									'admin/home/html',
									'admin/tpl/tag',
								)) || strpos($link['uri'], 'admin/field/index/rname/') !== false
								|| strpos($link['uri'], 'admin/module/config') !== false) {
								continue;
							}
							$this->db->insert('admin_menu', array(
								'pid' => $leftid,
								'uri' => dr_replace_m_uri($link, $id, $dir),
								'mark' => 'module-'.$dir,
								'name' => $link['name'],
								'icon' => $link['icon'] ? $link['icon'] : dr_get_icon($link['uri']),
								'hidden' => 0,
								'displayorder' => 0,
							));
						}
					}
					// 查询表单
					$form = $this->db->where('module', $dir)->get('module_form')->result_array();
					if ($form) {
						// 将此表单放在模块菜单中
						foreach ($form as $f) {
							$f['setting'] = dr_string2array($f['setting']);
							$this->db->insert('admin_menu', array(
								'uri' => $dir.'/admin/form_'.$f['table'].'/index',
								'url' => '',
								'pid' => $leftid,
								'name' => $f['name'].'管理',
								'icon' => $f['setting']['icon'] ? $f['setting']['icon'] : 'fa fa-th-large',
								'mark' => 'module-'.$dir.'-'.$f['id'],
								'hidden' => 0,
								'displayorder' => 0,
							));
						}
					}
				}
			} elseif ($menu['admin']) {
				// 插入后台的顶级菜单
				$this->db->insert('admin_menu', array(
					'uri' => '',
					'pid' => 0,
					'mark' => $menu['mark'] ? $menu['mark'] : 'module-'.$dir,
					'name' => $name,
					'icon' => $menu['icon'] ? $menu['icon'] : dr_get_icon_m($dir),
					'hidden' => 0,
					'displayorder' => 0,
				));
				$topid = $this->db->insert_id();
				$left_id = 0;
				foreach ($menu['admin'] as $left) { // 分组菜单名称
					$this->db->insert('admin_menu', array(
						'uri' => '',
						'pid' => $topid,
						'mark' => $left['mark'] ? $left['mark'] : 'module-'.$dir,
						'name' => $left['name'],
						'icon' => $left['icon'] ? $left['icon'] : dr_get_icon_left($left['name']),
						'hidden' => 0,
						'displayorder' => 0,
					));
					$leftid = $this->db->insert_id();
					$left_id = $left_id ? $left_id : $leftid;
					foreach ($left['menu'] as $link) { // 链接菜单
						$this->db->insert('admin_menu', array(
							'pid' => $leftid,
							'uri' => dr_replace_m_uri($link, $id, $dir),
							'mark' => 'module-'.$dir,
							'name' => $link['name'],
							'icon' => $link['icon'] ? $link['icon'] : dr_get_icon($link['uri']),
							'hidden' => 0,
							'displayorder' => 0,
						));
					}
				}
				// 查询表单
				$form = $this->db->where('module', $dir)->get('module_form')->result_array();
				if ($form && $left_id) {
					// 将此表单放在模块菜单中
					foreach ($form as $f) {
						$f['setting'] = dr_string2array($f['setting']);
						$this->db->insert('admin_menu', array(
							'uri' => $dir.'/admin/form_'.$f['table'].'/index',
							'url' => '',
							'pid' => $left_id,
							'name' => $f['name'].'管理',
							'icon' => $f['setting']['icon'] ? $f['setting']['icon'] : 'fa fa-th-large',
							'mark' => 'module-'.$dir.'-'.$f['id'],
							'hidden' => 0,
							'displayorder' => 0,
						));
					}
				}
			}

		}

	}
}