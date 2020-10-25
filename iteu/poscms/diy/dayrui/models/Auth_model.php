<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Auth_model extends CI_Model{
    
	/**
	 * 认证控制模型类
	 */
    public function __construct() {
        parent::__construct();
    }
    
    /**
	 * 审核流程
	 *
     * @param   intval
	 * @return	array
	 */
	public function get_verify($id) {
	
		$data = $this->db->where('id', $id)->get('admin_verify')->row_array();
        if (!$data) {
            return NULL;
        }
		
        $data['verify'] = dr_string2array($data['verify']);
		
        return $data;
	}
	
    /**
	 * 审核流程
	 *
	 * @return	array
	 */
	public function get_verify_all() {
	
		$data = $this->db->order_by('id ASC')->get('admin_verify')->result_array();
        if (!$data) {
            return NULL;
        }
		
        foreach ($data as $i => $t) {
            $t['verify'] = dr_string2array($t['verify']);
            $t['num'] = count($t['verify']);
            $data[$i] = $t;
        }
		
        return $data;
	}
    
	/**
	 * 管理员角色组
	 *
	 * @return	array	所有角色
	 */
	public function get_admin_role_all() {
	
		return $this->db->order_by('id ASC')->get('admin_role')->result_array();
	}
	
	/**
	 * 添加角色组
	 *
	 * @param	array	$data	添加数据
	 * @return	int		$id		角色id
	 */
	public function add_role($data) {
	
		if (!$data) {
            return NULL;
        }
		
		$this->db->insert('admin_role', array(
			'name' => $data['name'],
			'site' => dr_array2string($data['site']),
			'system' => '',
			'module' => '',
			'application' => '',
		));
		
		return $this->db->insert_id();
	}
	
	/**
	 * 修改角色组
	 *
	 * @param	array	$_data	老数据
	 * @param	array	$data	修改数据
	 * @return	int		$id		角色id
	 */
	public function edit_role($_data, $data) {
	
		if (!$data || !$_data) {
            return NULL;
        }
		
		$this->db->where('id', $_data['id'])->update('admin_role', array(
            'site' => dr_array2string($data['site']),
            'name' => $data['name'],
         ));
		
		return $_data['id'];
	}
	
	/**
	 * 更新权限
	 *
	 * @param	intval	$id		主键id
	 * @param	string	$name	权限名称
	 * @param	array	$data	权限数据
	 * @return	void
	 */
	public function update_auth($id, $name, $data) {
	
		if (!$id) {
            return NULL;
        }

        $app = $this->db->get('application')->result_array();
        if ($app) {
            foreach ($app as $i => $t) {
                $cfg = dr_string2array($t['setting']);
                $cfg['admin'][$id] = 0;
                if ($data) {
                    foreach ($data as $uri) {
                        if (strpos($uri, $t['dirname'].'/admin/') === 0) {
                            $cfg['admin'][$id] = 1;
                            break;
                        }
                    }
                }
                if (!$cfg['admin'][$id]) {
                    unset($cfg['admin'][$id]);
                }
                $this->db->where('id', $t['id'])->update('application', array(
                    'setting' => dr_array2string($cfg),
                ));
            }
        }

		$this->db->where('id', $id)->update('admin_role', array(
            'module' => '',
            'system' => dr_array2string($data),
            'application' => '',
        ));

		$this->role_cache();
	}

	/**
	 * 更新update_auth_user权限
	 *
	 * @param	intval	$uid
	 * @param	array	$data	权限数据
	 * @return	void
	 */
	public function update_auth_user($id, $data) {

		if (!$id) {
            return NULL;
        }

        $this->db->query('ALTER TABLE `'.$this->db->dbprefix.'admin` CHANGE `color` `color` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT \'\'');

		$this->db->where('uid', $id)->update('admin', array(
            'color' => dr_array2string($data),
        ));

        return $data;
	}
	
	/**
	 * 角色组数据
	 *
	 * @param	int		$id		主键id
	 * @return	array
	 */
	public function get_role($id) {
	
		if (!$id) {
            return NULL;
        }
		
		$data = $this->db->where('id', $id)->get('admin_role')->row_array();
		if (!$data) {
            return NULL;
        }
		
		$data['site'] = dr_string2array($data['site']);
		$data['system'] = dr_string2array($data['system']);
		$data['module'] = dr_string2array($data['module']);
		$data['application'] = dr_string2array($data['application']);

        $auth = $data['system'];
        $auth = $data['module'] ? $data['module'] + $auth : $auth;
        $auth = $data['application'] ? $data['application'] + $auth : $auth;

        $data['system'] = $auth;
        unset($data['module'], $data['application'], $auth);

		return $data;
	}
	
	/**
	 * 批量删除角色组
	 *
	 * @param	array	$ids	主键id
	 * @return	NULL
	 */
	public function del_role_all($ids) {
	
		if (!$ids) {
            return NULL;
        }
		
		$this->db->where_in('id', $ids)->where('id<>1')->delete('admin_role');
			 
		return NULL;
	}
	
	/**
	 * 删除角色组
	 *
	 * @param	int	$id	主键id
	 * @return	NULL
	 */
	public function del_role($id) {
	
		if (!$id || $id == 1) {
            return NULL;
        }
		
		$this->db->where('id', $id)->delete('admin_role');
			 
		return NULL;
	}
	
	/**
	 * 获取权限选项部分
	 *
	 * @param	array	$config		配置数据
	 * @param	string	$app_dir	应用/模块目录
	 * @return	array
	 */
	private function get_auth_uri($config, $app_dir = NULL) {
	
		if (!$config || !$config['auth']) {
            return NULL;
        }
		
		$data = array();
		foreach ($config['auth'] as $x) {
			if (!$x['auth']) {
				continue;
			}
			foreach ($x['auth'] as $uri => $xx) {
                $uri = ($app_dir ? $app_dir.'/' : '').trim($uri, '/');
				$data[$uri] = $xx;
			}
		}
		
		return $data;
	}
	
	/**
	 * 获取所有权限选项
	 *
	 * @return	array
	 */
	public function get_auth_all() {

		// 系统权限
		$config = array();
		require WEBPATH.'config/auth.php';
		$data = $this->get_auth_uri($config);
		// 分支系统权限
		if (is_file(WEBPATH.'config/auth_branch.php')) {
			$config = array();
			require WEBPATH.'config/auth_branch.php';
			$data2 = $this->get_auth_uri($config);
			$data2 && $data = $data + $data2;
			unset($config);
		}

		// 模块权限
        $module = $this->db->where('disabled', 0)->get('module')->result_array();
		if ($module) {
			foreach ($module as $t) {
				if (is_file(FCPATH.'module/'.$t['dirname'].'/config/auth.php')) {
					$config = array();
					require FCPATH.'module/'.$t['dirname'].'/config/auth.php';
                    foreach ($config['auth'] as $i => $c) {
                        if (isset($c['auth']['admin/tpl/index'])) {
                            // 增加移动端模板权限
                            $config['auth'][$i]['auth']['admin/tpl/mobile'] = fc_lang('移动端模板');
                            break;
                        }
                    }
					$data = array_merge($data, $this->get_auth_uri($config, $t['dirname']));
				}
			}
		}

		// 应用权限
        $app = $this->db->where('disabled', 0)->get('application')->result_array();
		if ($app) {
			foreach ($app as $t) {
				if (is_file(FCPATH.'app/'.$t['dirname'].'/config/auth.php')) {
					$config = array();
					require FCPATH.'app/'.$t['dirname'].'/config/auth.php';
					$config && $data = @array_merge($data, $this->get_auth_uri($config, $t['dirname']));
				}
			}
		}

		return $data;
	}
	
	/**
	 * 更新角色缓存
	 *
	 * @return	void
	 */
	public function role_cache() {
	
		$data = $this->get_admin_role_all();
		if (!$data) {
            return NULL;
        }
		
		$this->dcache->delete('role');
		$this->ci->clear_cache('role');
		
		$cache = array();
		foreach ($data as $t) {
			$t['site'] = dr_string2array($t['site']);
			$t['system'] = dr_string2array($t['system']);
			$t['module'] = dr_string2array($t['module']);
			$t['application'] = dr_string2array($t['application']);
			$cache[$t['id']] = $t;
		}
		
		$this->dcache->set('role', $cache);
		
		return $cache;
	}

	// 获取权限级别, 1比ta大，0比ta小
	public function role_level($myid, $taid) {

		if ($myid == 1) {
			return 1;
		} elseif ($taid == 1) {
			return 0;
		} elseif ($myid == $taid) {
			return 1; // 一样大
		}

		$role = $this->ci->get_cache('role');
		if (!isset($role[$myid])) {
			return 0;
		} elseif (!isset($role[$taid])) {
			return 1;
		}

		$diff = array_diff($role[$myid]['system'], $role[$taid]['system']);
		if (!$diff) {
			// 不存在差集时，并且数量一致表示两个组权限相同, 否则比他小
			if (count($role[$myid]['system']) == count($role[$taid]['system'])) {
				return 1;
			} else {
				return 0;
			}
		}

		foreach ($diff as $t) {
			if (in_array($t, $role[$myid]['system'])) {
				// 当权一个在我的权限中时，表示我最大
				return 1;
			} else {
				return 0;
			}
		}

		return 0;
	}
}