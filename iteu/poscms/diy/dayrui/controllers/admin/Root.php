<?php


/* v3.1.0  */
	
class Root extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('管理员管理') => array('admin/root/index', 'users'),
		    fc_lang('添加') => array('admin/root/add_js', 'plus-square')
		)));
    }
	
	/**
     * 管理员管理
     */
    public function index() {

		if (IS_POST && $_POST['action'] == 'del') {
			$ids = $this->input->post('ids');
			if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            } elseif (!$this->is_auth('admin/root/del')) {
                exit(dr_json(0, fc_lang('您无权限操作')));
            }
			foreach ($ids as $id) {
				// 认证权限
				$data = $this->member_model->get_admin_member($id);
				if (!$this->auth_model->role_level($this->member['adminid'], $data['adminid'])) {
					exit(dr_json(0, fc_lang('您无权操作（ta的权限高于你）')));
				} elseif ($id == 1) {
					exit(dr_json(0, fc_lang('无法删除创始人管理权限')));
				}
				$this->member_model->del_admin($id);
			}
            $this->system_log('删除后台管理员【#'.@implode(',', $ids).'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), ''));
		}
		
		$this->template->assign('list', $this->member_model->get_admin_all((int)$this->input->get('roleid'), $this->input->post('keyword', TRUE)));
		$this->template->display('admin_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
	
		$role = $this->dcache->get('role');
		
		if (IS_POST) {
		
			$data = $this->input->post('data', TRUE);
			(!$data['adminid'] || !isset($role[$data['adminid']])) && exit(dr_json(0, fc_lang('请选择角色组或者角色组不存在'), 'adminid'));
            !$this->auth_model->role_level($this->member['adminid'], $data['adminid']) && exit(dr_json(0, fc_lang('您无权操作（ta的权限高于你）')));
			
			$check = $this->db
                          ->select('uid,adminid')
                          ->where('username', $data['username'])
                          ->limit(1)
                          ->get($this->db->dbprefix('member'))
                          ->row_array();
			$uid = $check['uid'];
			
			if (!$check) { // 会员不存在时，需要注册
				$member = array(
					'username' => $data['username'],
					'password' => trim($data['password']),
					'phone' => $data['phone'] ? $data['phone'] : '',
					'email' => $data['email']
				);
				$uid = $this->member_model->register($member, 3);
				if ($uid == -1) {
					exit(dr_json(0, fc_lang('该会员【%s】已经被注册', $data['username']), 'username'));
				} elseif ($uid == -2) {
					exit(dr_json(0, fc_lang('邮箱格式不正确'), 'email'));
				} elseif ($uid == -3) {
					exit(dr_json(0, fc_lang('该邮箱【%s】已经被注册', $data['email']), 'email'));
				} elseif ($uid == -4) {
					exit(dr_json(0, fc_lang('同一IP在限制时间内注册过多'), 'username'));
				} elseif ($uid == -5) {
					exit(dr_json(0, fc_lang('Ucenter：会员名称不合法'), 'username'));
				} elseif ($uid == -6) {
					exit(dr_json(0, fc_lang('Ucenter：包含不允许注册的词语'), 'username'));
				} elseif ($uid == -7) {
					exit(dr_json(0, fc_lang('Ucenter：Email格式有误'), 'username'));
				} elseif ($uid == -8) {
					exit(dr_json(0, fc_lang('Ucenter：Email不允许注册'), 'username'));
				} elseif ($uid == -9) {
					exit(dr_json(0, fc_lang('Ucenter：Email已经被注册'), 'username'));
				} elseif ($uid == -10) {
					exit(dr_json(0, fc_lang('手机号码必须是11位的整数'), 'phone'));
				} elseif ($uid == -11) {
					exit(dr_json(0, fc_lang('该手机号码已经注册'), 'phone'));
				}
			} elseif ($check['adminid'] > 0) { // 已经属于管理组
				exit(dr_json(0, fc_lang('该会员已经是管理组了'), 'username'));
			}
			
			$menu = array();
			if ($data['usermenu']) {
				foreach ($data['usermenu']['name'] as $id => $v) {
					$v && $data['usermenu']['url'][$id] && $menu[$id] = array('name' => $v, 'url' => $data['usermenu']['url'][$id]);
                }
			}
			
			$insert	= array(
				'uid' => $uid,
				'realname' => $data['realname'],
				'usermenu' => dr_array2string($menu)
			);
			$update	= array('adminid' => $data['adminid']);
            $this->system_log('添加后台管理员【#'.$uid.'】'.$data['realname']); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), $this->member_model->insert_admin($insert, $update, $uid)));
		}
		
		$this->template->assign('role', $role);
		$this->template->display('admin_add.html');
    }

	/**
     * 修改
     */
    public function edit() {
	
		$uid = (int)$this->input->get('id');
		$data = $this->member_model->get_admin_member($uid);
		!$data && exit(fc_lang('对不起，数据被删除或者查询不存在'));

		// 认证权限
		!$this->auth_model->role_level($this->member['adminid'], $data['adminid']) && exit(fc_lang('您无权操作（ta的权限高于你）'));
		
		$role = $this->dcache->get('role');
		
		if (IS_POST) {
			$menu = array();
			$data = $this->input->post('data', TRUE);
			(!$data['adminid'] || !isset($role[$data['adminid']])) && exit(dr_json(0, fc_lang('请选择角色组或者角色组不存在'), 'adminid'));
            if ($data['usermenu']) {
				foreach ($data['usermenu']['name'] as $id => $v) {
					$v && $data['usermenu']['url'][$id] && $menu[$id] = array('name' => $v, 'url' => $data['usermenu']['url'][$id]);
                }
			}
			$insert	= array(
				'uid' => $uid,
				'realname' => $data['realname'],
				'usermenu' => dr_array2string($menu)
			);
			$update	= array('adminid' => $data['adminid']);
            $this->system_log('修改后台管理员【#'.$uid.'】'.$data['realname']); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), $this->member_model->update_admin($insert, $update, $uid)));
		}
		
		$this->template->assign(array(
			'role' => $role,
			'data' => $data
		));
		$this->template->display('admin_add.html');
    }
	
	/**
     * 修改资料
     */
    public function my() {

		if (IS_POST) {
			$menu = array();
			$data = $this->input->post('data', TRUE);
			$password = trim($data['password']);
			if ($data['usermenu']) {
				foreach ($data['usermenu']['name'] as $id => $v) {
					$v && $data['usermenu']['url'][$id] && $menu[$id] = array('name' => $v, 'url' => $data['usermenu']['url'][$id]);
                }
			}
            // 修改密码
			if ($password) {
				defined('UC_KEY') && uc_user_edit($this->member['username'], '', $password, '', 1);
				$this->db->where('uid', $this->uid)->update('member', array(
                    'password' => md5(md5($password).$this->member['salt'].md5($password))
                ));
			}
			$this->db->where('uid', $this->uid)->update('admin', array(
                'color' => $data['color'],
                'realname' => $data['realname'],
                'usermenu' => dr_array2string($menu)
            ));
            $this->system_log('修改后台管理员资料【#'.$this->uid.'】'); // 记录日志
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('root/my'), 1);
		} else {
			$this->template->assign('color', array(
				'default' => '#333438',
				'blue' => '#368ee0',
				'blue' => '#368ee0',
				'darkblue' => '#2b3643',
				'grey' => '#697380',
				'light' => '#F9FAFD',
				'light2' => '#F1F1F1',
				//'v2' => '#368ee0',
			));
			$this->template->display('admin_my.html');
		}
    }
	
	/**
     * 登录日志
     */
    public function log() {

		list($data, $total, $param)	= $this->_limit_page(); // 数据库中分页查询
		
		$uid = (int)$param['uid'];
        $this->load->library('dip');

		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('管理员管理') => array('admin/root/index', 'users'),
				fc_lang('添加') => array('admin/root/add_js', 'plus-square'),
				fc_lang('登录日志') => array('admin/root/log/'.(isset($_GET['uid']) && $_GET['uid'] ? 'uid/'.$uid : ''), 'calendar')
			)),
			'list' => $data,
			'total' => $total,
			'param' => $param,
			'auser' => $this->db->where('uid', $uid)->get('admin')->row_array(),
			'pages'	=> $this->get_pagination(dr_url('admin/root/log', array(
				'uid' => $uid,
				'total' => $total,
				'search' => IS_POST ? 1 : $this->input->get('search')
			)), $total),
		));
		$this->template->display('admin_log.html');
	}
	
	/**
     * 删除
     */
    public function del() {

        $id = (int)$this->input->get('id');
		$data = $this->member_model->get_admin_member($id);
		// 认证权限
		if (!$this->auth_model->role_level($this->member['adminid'], $data['adminid'])) {
			exit(dr_json(0, fc_lang('您无权操作（ta的权限高于你）')));
		} elseif ($id == 1) {
			exit(dr_json(0, fc_lang('无法删除创始人管理权限')));
		}

		$this->member_model->del_admin($id);
        $this->system_log('删除后台管理员资料【#'.$id.'】'); // 记录日志
		exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
	}
	
	/**
     * 检查用户情况
     */
	public function check_username() {
		$result = $this->db
                       ->select('uid,adminid')
                       ->where('username', $this->input->post('username', TRUE))
                       ->limit(1)
                       ->get($this->db->dbprefix('member'))
                       ->row_array();
		!$result && exit(dr_json(1, ''));
        // 不存在，注册新会员
		$result['adminid'] > 0 && exit(dr_json(2, fc_lang('该会员已经是管理组了')));
        // 已经属于管理组
		exit(dr_json(0, '', $result['uid'])); // 已经注册会员
	}
	
	private function _where(&$select) {
	
		$uid = (int)$this->input->get('uid');
        $uid = $uid ? $uid : $this->uid;
		$search = $this->input->get('search');
		$cache_file = md5($this->duri->uri(1).$uid.$this->uid.SITE_ID.$this->input->ip_address().$this->input->user_agent()); // 缓存文件名称
		
		// 存在POST提交时，重新生成缓存文件
		if (IS_POST) {
			$data = $this->input->post('data');
			$this->cache->file->save($cache_file, $data, 3600);
			$search = 1;
			unset($_GET['page']);
		}
		
		$select->where('uid', $uid);
		
		// 存在search参数时，读取缓存文件
		if ($search == 1) {
			$data = $this->cache->file->get($cache_file);
			if (isset($data['start']) && $data['start'] && $data['start'] != $data['end']) {
				$data['end'] = $data['end'] ? $data['end'] : SYS_TIME;
				$select->where('logintime BETWEEN '.$data['start'].' AND '. $data['end']);
			}
		}
		
		$data['uid'] = $uid;

		return $data;
	}
	
	/**
	 * 后台数据分页显示
	 *
	 * @return	array	
	 */
	private function _limit_page() {
	
		$page = max((int)$this->input->get('page'), 1);
		$_total = (int)$this->input->get('total');
		
		if (!$_total) {
			$select = $this->db;
			$_param = $this->_where($select);
			$_total = $this->db->count_all_results('admin_login');
			if (!$_total) {
                return array(array(), 0, $_param);
            }
		}

        $select = $this->db;
		$_param = $this->_where($select);
		$result = $this->db
					   ->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1))
					   ->order_by('logintime DESC')
					   ->get('admin_login')
					   ->result_array();
					   
		return array($result, $_total, $_param);
	}


}