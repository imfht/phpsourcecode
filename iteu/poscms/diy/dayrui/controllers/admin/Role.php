<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Role extends M_Controller {
	
	private $_menu;
	private $_auth;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->_menu = array(
		    fc_lang('角色管理') => array('admin/role/index', 'user'),
		    fc_lang('添加') => array('admin/role/add_js', 'plus'),
		);
		$this->template->assign('menu', $this->get_menu_v3($this->_menu));
    }
	
	/**
     * 权限组管理
     */
    public function index() {
	
		if (IS_POST) {
			$ids = $this->input->post('ids');
			if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            foreach ($ids as $i) {
                if (!$this->auth_model->role_level($this->member['adminid'], $i)) {
                    exit(dr_json(0, fc_lang('您无权操作（ta的权限高于你）')));
                }
		    }
            $code = $this->auth_model->del_role_all($ids);
            $this->auth_model->role_cache();
            $this->system_log('删除后台权限组【#'.@implode(',', $ids).'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功'), $code));
		}
		
		$this->template->assign('list', $this->auth_model->get_admin_role_all());
		$this->template->display('role_index.html');
    }
	
	/**
     * 添加组
     */
    public function add() {

		if (IS_POST) {
            $data = $this->input->post('data');
            $code = $this->auth_model->add_role($data);
            $this->auth_model->role_cache();
            $this->system_log('添加后台权限组【#'.$code.'】'.$data['name']); // 记录日志
            exit(dr_json(1, fc_lang('操作成功'), $code));
        }

		$this->template->display('role_add.html');
    }

	/**
     * 修改组
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
        if (!$this->auth_model->role_level($this->member['adminid'], $id)) {
            exit(fc_lang('您无权操作（ta的权限高于你）'));
        }

		$data = $this->db->where('id', $id)->get('admin_role')->row_array();
		if (!$data) {
            exit(fc_lang('对不起，数据被删除或者查询不存在'));
        }
		
		if (IS_POST) {
            $post = $this->input->post('data');
            $code = $this->auth_model->edit_role($data, $post);
            $this->auth_model->role_cache();
            $this->system_log('修改后台权限组【#'.$code.'】'.$post['name']); // 记录日志
            exit(dr_json(1, fc_lang('操作成功'), $code));
        }

		$data['site'] = dr_string2array($data['site']);
		
		$this->template->assign('data', $data);
        $this->template->display('role_add.html');
    }
	
	/**
     * 删除组
     */
    public function del() {
        $id = (int)$this->input->get('id');
        if (!$this->auth_model->role_level($this->member['adminid'], $id)) {
            exit(dr_json(0, fc_lang('您无权操作（ta的权限高于你）')));
        }
		$this->auth_model->del_role($id);
        $this->auth_model->role_cache();
        $this->system_log('删除后台权限组【#'.$id.'】'); // 记录日志
		exit(dr_json(1, fc_lang('操作成功')));
	}
	
	/**
     * 权限划分
     */
	public function auth() {
	
		$id = (int)$this->input->get('id');
		if ($id == 1) {
            $this->admin_msg(fc_lang('超级管理员拥有最高权限，不需要配置'));
        } elseif (!$this->auth_model->role_level($this->member['adminid'], $id)) {
            $this->admin_msg(fc_lang('您无权操作（ta的权限高于你）'));
        }
		
		if (IS_POST) {
            $this->auth_model->update_auth($id, 'system', $this->input->post('data'));
            $this->auth_model->role_cache();
            $this->system_log('设置后台权限组【#'.$id.'】权限'); // 记录日志
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
        }

        $this->load->model('menu_model');
        $menu = $this->menu_model->cache();
        $link = $this->dcache->get('link');
		$data = $this->auth_model->get_role($id);
        $auth = $this->auth_model->get_auth_all();

        $_index = $_auth = $this->_auth = array();
        foreach ($auth as $uri => $name) {
            $arr = @explode('/', $uri);
            if (end($arr) == 'index') {
                $_index[$uri] = array();
            }
            $_auth[$uri] = $name;
        }

        // 归类
        foreach ($_index as $uri => $t) {
            $uri1 = str_replace('/index', '', $uri);
            foreach ($_auth as $uri2 => $name2) {
                if (strpos($uri2, $uri1) === 0) {
                    if ($uri == 'admin/attachment/index'
                        && strpos($uri2, 'admin/attachment2') === 0) {
                        continue;
                    }
                    $this->_auth[$uri][$uri2] = $name2;
                }
            }
        }

        $MOD = $this->db->where('disabled', 0)->get('module')->result_array();
        $mod_count = $mod_site = array();
        if ($MOD) {
            foreach ($MOD as $m) {
                $mod_count[$m['dirname']] = 0;
                $table = $this->db->dbprefix(SITE_ID.'_'.$m['dirname'].'_category');
                if (!$this->db->query("SHOW TABLES LIKE '".$table."'")->row_array()) {
                    continue;
                }
                $site = dr_string2array($m['site']);
                if (isset($site[SITE_ID]) && $site[SITE_ID]['use']) {
                    $mod_site[] = $m['dirname'];
                }
                $category = $this->db->get($table)->result_array();
                foreach ($category as $cat) {
                    // 跳过有下级栏目的判断
                    if ($cat['child']) {
                        continue;
                    }
                    $cat['setting'] = dr_string2array($cat['setting']);
                    // 当栏目中存在一项是管理就标识为管理状态
                    if (isset($cat['setting']['admin'][$id]['show'])
                        && $cat['setting']['admin'][$id]['show'] == 1) {
                        $mod_count[$m['dirname']] ++ ;
                    }
                }
                // 模块表单权限
                $form = $this->db->where('module', $m['dirname'])->get('module_form')->result_array();
                if ($form) {
                    foreach ($form as $t) {
                        $this->_auth[$m['dirname'].'/admin/form_'.$t['table'].'/index'] = array(
                            $m['dirname'].'/admin/form_'.$t['table'].'/index' => fc_lang($t['name']),
                            $m['dirname'].'/admin/form_'.$t['table'].'/add' => fc_lang('添加'),
                            $m['dirname'].'/admin/form_'.$t['table'].'/edit' => fc_lang('修改'),
                            $m['dirname'].'/admin/form_'.$t['table'].'/del' => fc_lang('删除'),
                            $m['dirname'].'/admin/form_'.$t['table'].'/show' => fc_lang('查看'),
                        );
                    }
                }
            }
        }

        // 查询网站表单
        $form = $this->db->get(SITE_ID.'_form')->result_array();
        if ($form) {
            foreach ($form as $t) {
                $this->_auth['admin/form_'.$t['table'].'/index'] = array(
                    'admin/form_'.$t['table'].'/index' => fc_lang($t['name']),
                    'admin/form_'.$t['table'].'/add' => fc_lang('添加'),
                    'admin/form_'.$t['table'].'/edit' => fc_lang('修改'),
                    'admin/form_'.$t['table'].'/del' => fc_lang('删除'),
                    'admin/form_'.$t['table'].'/show' => fc_lang('查看'),
                );
            }
        }

		$this->_menu[$data['name']] = array('admin/role/auth/id/'.$id, 'user');

		$this->template->assign(array(
            'data' => $data,
			'menu' => $this->get_menu_v3($this->_menu),
			'amenu' => $menu,
            'mlink' => $link,
            'myrole' => array_merge(
                $this->admin['role']['system'],
                $this->admin['role']['module'],
                $this->admin['role']['application']
            ),
            'not_auth' => array(
                'admin/home/main',
                'admin/root/my',
                'admin/check/index',
                'admin/home/clear',
                'admin/home/cache',
            ),
            'syslink' => array(
                array('uri' => 'admin/route/index', 'name' => fc_lang('生成伪静态')),
            ),
            'mod_site' => $mod_site,
            'mod_count' => $mod_count,
		));
        $this->template->display('role_auth.html');
	}


	/**
     * 权限划分为账号
     */
	public function user() {

		$uid = (int)$this->input->get('uid');
        //;
        $user = $this->member_model->get_admin_member($uid);
        if (!$user) {
            $this->admin_msg(fc_lang('管理员账号不存在'));
        } elseif ($user['adminid'] == 1) {
            $this->admin_msg(fc_lang('超级管理员拥有最高权限，不需要配置'));
        } elseif (!$this->auth_model->role_level($this->member['adminid'], $user['adminid'])) {
            $this->admin_msg(fc_lang('您无权操作（ta的权限高于你）'));
        }

		if (IS_POST) {
            $post = $this->input->post('data');
            if ($post) {
                $this->auth_model->update_auth_user($uid, $post);
            } else {
                $this->db->where('uid', $uid)->update('admin', array('color' => ''));
            }
            $this->system_log('设置后台管理员【#'.$user['username'].'】权限'); // 记录日志
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
        }

        $this->load->model('menu_model');
        $menu = $this->menu_model->cache();
        $link = $this->dcache->get('link');
        $auth = $this->auth_model->get_auth_all();

        $_index = $_auth = $this->_auth = array();
        foreach ($auth as $uri => $name) {
            $arr = @explode('/', $uri);
            if (end($arr) == 'index') {
                $_index[$uri] = array();
            }
            $_auth[$uri] = $name;
        }

        // 归类
        foreach ($_index as $uri => $t) {
            $uri1 = str_replace('/index', '', $uri);
            foreach ($_auth as $uri2 => $name2) {
                if (strpos($uri2, $uri1) === 0) {
                    if ($uri == 'admin/attachment/index'
                        && strpos($uri2, 'admin/attachment2') === 0) {
                        continue;
                    }
                    $this->_auth[$uri][$uri2] = $name2;
                }
            }
        }

        $MOD = $this->db->where('disabled', 0)->get('module')->result_array();
        $mod_count = $mod_site = array();
        if ($MOD) {
            foreach ($MOD as $m) {
                $mod_count[$m['dirname']] = 0;
                $table = $this->db->dbprefix(SITE_ID.'_'.$m['dirname'].'_category');
                if (!$this->db->query("SHOW TABLES LIKE '".$table."'")->row_array()) {
                    continue;
                }
                // 模块表单权限
                $form = $this->db->where('module', $m['dirname'])->get('module_form')->result_array();
                if ($form) {
                    foreach ($form as $t) {
                        $this->_auth[$m['dirname'].'/admin/form_'.$t['table'].'/index'] = array(
                            $m['dirname'].'/admin/form_'.$t['table'].'/index' => fc_lang($t['name']),
                            $m['dirname'].'/admin/form_'.$t['table'].'/add' => fc_lang('添加'),
                            $m['dirname'].'/admin/form_'.$t['table'].'/edit' => fc_lang('修改'),
                            $m['dirname'].'/admin/form_'.$t['table'].'/del' => fc_lang('删除'),
                            $m['dirname'].'/admin/form_'.$t['table'].'/show' => fc_lang('查看'),
                        );
                    }
                }
            }
        }

        // 查询网站表单
        $form = $this->db->get(SITE_ID.'_form')->result_array();
        if ($form) {
            foreach ($form as $t) {
                $this->_auth['admin/form_'.$t['table'].'/index'] = array(
                    'admin/form_'.$t['table'].'/index' => fc_lang($t['name']),
                    'admin/form_'.$t['table'].'/add' => fc_lang('添加'),
                    'admin/form_'.$t['table'].'/edit' => fc_lang('修改'),
                    'admin/form_'.$t['table'].'/del' => fc_lang('删除'),
                    'admin/form_'.$t['table'].'/show' => fc_lang('查看'),
                );
            }
        }

		$this->_menu[$user['username']] = array('admin/role/user/uid/'.$uid, 'user');

		$this->template->assign(array(
            'user' => $user,
            'data' => array(
                'system' => $user['color'],
            ),
			'menu' => $this->get_menu_v3($this->_menu),
			'amenu' => $menu,
            'mlink' => $link,
            'myrole' => array(

            ),
            'not_auth' => array(
                'admin/home/main',
                'admin/root/my',
                'admin/check/index',
                'admin/home/clear',
                'admin/home/cache',
            ),
            'syslink' => array(
                array('uri' => 'admin/route/index', 'name' => fc_lang('生成伪静态')),
            ),
            'mod_site' => $mod_site,
            'mod_count' => $mod_count,
		));
        $this->template->display('role_user.html');
	}

    //
    public function _get_auth($uri) {

        if (!$uri) {
            return;
        }

        $arr = @explode('/', $uri);
        if (end($arr) != 'index') {
            return;
        }

        return $this->_auth[$uri];

    }
	
	/**
     * 缓存
     */
	public function cache() {
        $this->auth_model->role_cache();
        (int)$_GET['admin'] or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}

}