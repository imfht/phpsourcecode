<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Application_model extends CI_Model {
	
	/**
	 * 应用模型类
	 */
    public function __construct() {
        parent::__construct();
	}
	
	/**
	 * 所有应用
	 *
	 * @return	array
	 */
	public function get_data() {
	
		$data = $this->db->order_by('id ASC')->get('application')->result_array();
		if (!$data) {
            return NULL;
        }
		
		$app = array();
		foreach ($data as $t) {
			$t['module'] = dr_string2array($t['module']);
			$t['setting'] = dr_string2array($t['setting']);
			$app[$t['dirname']] = $t;
		}
		
		return $app;
	}
	
	/**
	 * 应用数据
	 *
	 * @param	string	$dir
	 * @return	array
	 */
	public function get($dir) {
	
		$data = $this->db->where('dirname', $dir)->get('application')->row_array();
		if (!$data) {
            return NULL;
        }
		
		$data['module'] = dr_string2array($data['module']);
		$data['setting'] = dr_string2array($data['setting']);
		
		return $data;
	}
	
	/**
	 * 应用入库
	 *
	 * @param	string	$dir
	 * @return	intval
	 */
	public function add($dir) {
	
		if (!$dir) {
            return NULL;
        }
		
		$this->db->insert('application', array(
			'module' => '',
			'dirname' => $dir,
			'setting' => '',
			'disabled' => 0,
		));
		$id = $this->db->insert_id();
		if (!$id) {
            return NULL;
        }
		
		return $id;
	}
	
	/**
	 * 修改应用配置
	 *
	 * @param	intval	$id
	 * @param	array	$data
	 * @return	bool
	 */
	public function edit($id, $data) {
	
		if (!$id) {
            return FALSE;
        }
		
		$this->db->where('id', (int)$id)->update('application', $data);
		
		return TRUE;
	}
	
	/**
	 * 删除应用
	 *
	 * @param	intval	$id
	 * @return	bool
	 */
	public function del($id) {
	
		if (!$id) {
            return FALSE;
        }
		
		$this->db->where('id', (int)$id)->delete('application');
		$this->cache();
			 
		return TRUE;
	}
	
	/**
	 * 应用缓存
	 */
	public function cache() {

        $cache = array();

        // 删除应用缓存
		$this->dcache->delete('app');

        // 搜索本地应用
		$this->load->helper('system');
        $local = dr_dir_map(FCPATH.'app/', 1);
        if ($local) {
            $link = $this->dcache->get('link');
            $role = $this->db->where('id>1')->get('admin_role')->result_array();
            $role_cache = 0;
            foreach ($local as $dir) {
                $app = $this->db->where('dirname', $dir)->where('disabled', 0)->get('application')->row_array();
                if (is_file(FCPATH.'app/'.$dir.'/config/app.php') && $app) {
                    // 保存缓存
                    $cache[] = $dir;
                    $menu = $this->db->where('pid', 75)->where('mark', 'app-'.$dir)->get('admin_menu')->row_array();
					$menu['hidden'] !== 0 && $this->db->where('id', $menu['id'])->update('admin_menu', array('hidden' => 0));
                    //同步到角色组权限
                    if ($role && $menu) {
                        foreach ($role as $t) {
                            $cfg = dr_string2array($app['setting']);
                            $auth = dr_string2array($t['system']);
                            if ($cfg['admin'][$t['id']]) {
                                if (!in_array($menu['uri'], $auth)) {
                                    $auth[] = $menu['uri'];
                                    $this->db->where('id', $t['id'])->update('admin_role', array(
                                        'system' => dr_array2string($auth)
                                    ));
                                    $role_cache = 1;
                                }
                            } else {
                                if (in_array($menu['uri'], $auth)) {
                                    $temp = array_flip($auth);
                                    unset($temp[$menu['uri']]);
                                    $this->db->where('id', $t['id'])->update('admin_role', array(
                                        'system' => dr_array2string(array_flip($temp))
                                    ));
                                    $role_cache = 1;
                                }
                            }
                        }
                    }
                    if ($role_cache) {
                        $this->load->model('auth_model');
                        $this->auth_model->role_cache();
                    }
                } else {
                    // 删除菜单
                    $this->db->where('mark', 'app-'.$dir)->delete('admin_menu');
                    $this->db->where('mark', 'app-'.$dir)->delete('member_menu');
                }
            }
        }

		$this->dcache->set('app', $cache);

        return $cache;
	}
}