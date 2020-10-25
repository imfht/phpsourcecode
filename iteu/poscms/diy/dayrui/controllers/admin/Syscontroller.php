<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Syscontroller extends M_Controller {

    public $app;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('自定义控制器') => array('admin/syscontroller/index', 'code'),
		    fc_lang('添加') => array('admin/syscontroller/add', 'plus'),
		)));
        $this->app = array(
            0 => fc_lang('网站'),
            1 => fc_lang('会员'),
            2 => fc_lang('模块'),
        );
    }
	
	/**
     * 管理
     */
    public function index() {
	
		if (IS_POST) {
			$ids = $this->input->post('ids', TRUE);
			if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            } elseif (!$this->is_auth('admin/syscontroller/del')) {
                exit(dr_json(0, fc_lang('您无权限操作')));
            }
            $data = $this->db->where_in('id', $ids)->get('controller')->result_array();
            if ($data) {
                foreach ($data as $t) {
                    @unlink(FCPATH.$t['file']);
                    $this->db->where('id', $t['id'])->delete('controller');
                    $this->system_log('删除自定义控制器【#'.$t['file'].'】'); // 记录日志
                }
            }
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}

        $page = max(1, (int)$_GET['page']);
        $total = $_GET['total'] ? $_GET['total'] : $this->db->count_all_results('controller');

        $data = $total ? $this->db->order_by('id desc')->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1))->get('controller')->result_array() : array();

		
		$this->template->assign(array(
            'list' => $data,
            'total' => $total,
            'pages' => $this->get_pagination(dr_url('syscontroller/index', array('total' => $total)), $total),
        ));
		$this->template->display('syscontroller_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
	
		if (IS_POST || $_GET['post']) {
            $app = $this->input->post('app');
			$data = $this->input->post('data');
			if (!$data['name']) {
                exit(dr_json(0, fc_lang('【%s】不能为空', fc_lang('名称')), 'name'));
            } elseif (!$data['cname'] || !preg_match('/^[a-z]+[a-z0-9_\-]+$/i', $data['cname'])) {
                exit(dr_json(0, fc_lang('【%s】格式不正确', fc_lang('控制器名')), 'cname'));
            }
            $file = '';
            $name = ucfirst($data['cname']);
            if ($app == 0) {
                if (strlen($data['type'][0]) == 0) {
                    exit(dr_json(0, fc_lang('【%s】未选择', fc_lang('文件'))));
                }
                $data['app'] = '';
                $data['type'] = $data['type'][0];
                if ($data['type'] == 0) {
                    $file = 'dayrui/controllers/'.$name.'.php';
                } elseif ($data['type'] == 2) {
                    $file = 'dayrui/controllers/admin/'.$name.'.php';
                }
            } elseif ($app == 1) {
                if (strlen($data['type'][1]) == 0) {
                    exit(dr_json(0, fc_lang('【%s】未选择', fc_lang('文件'))));
                }
                $data['app'] = 'member';
                $data['type'] = $data['type'][1];
                if ($data['type'] == 1) {
                    $file = 'module/member/controllers/'.$name.'.php';
                } elseif ($data['type'] == 2) {
                    $file = 'module/member/controllers/admin/'.$name.'.php';
                }
            } elseif ($app == 2) {
                $dir = $this->input->post('dir');
                if (!$dir) {
                    exit(dr_json(0, fc_lang('【%s】未选择', fc_lang('模块'))));
                }
                if (strlen($data['type'][2]) == 0) {
                    exit(dr_json(0, fc_lang('【%s】未选择', fc_lang('文件'))));
                }
                $data['app'] = $dir;
                $data['type'] = $data['type'][2];
                if ($data['type'] == 0) {
                    $file = 'module/'.$dir.'/controllers/'.$name.'.php';
                } elseif ($data['type'] == 2) {
                    $file = 'module/'.$dir.'/controllers/admin/'.$name.'.php';
                } elseif ($data['type'] == 1) {
                    $file = 'module/'.$dir.'/controllers/member/'.$name.'.php';
                }
            }
            if (is_file(FCPATH.$file)) {
                exit(dr_json(0, fc_lang('文件【%s】已经存在', $file)));
            }
            $data['url'] = '';
            $data['file'] = $file;
            $data['inputtime'] = SYS_TIME;
			$this->db->insert('controller', $data);
            $id = $this->db->insert_id();
            // 创建文件
            $code = file_get_contents(WEBPATH.'cache/install/sysc.php');
            $code = str_replace(
                array('{name}', '{cname}', '{icname}', '{id}'),
                array($data['name'], $data['cname'], $name, $id),
                $code
            );
            $a = file_put_contents(FCPATH.$file, $code);
            if (!$a) {
                $this->db->where('id', $id)->delete('controller');
                exit(dr_json(0, fc_lang('文件【%s】创建失败，请检查权限', $file)));
            }
            $this->system_log('添加除自定义控制器【#'.$id.'】'); // 记录日志
			exit(dr_json(1, fc_lang('控制器创建成功，马上进入详情界面'), $id));
		}

        $dir = array();
        $local = @array_diff(dr_dir_map(FCPATH.'module/', 1), array('member')); // 搜索本地模块
        if ($local) {
            foreach ($local as $m) {
                if (is_file(FCPATH.'module/'.$m.'/config/module.php')) {
                    $dir[] = $m;
                }
            }
        }

        $this->template->assign(array(
            'dir' => $dir,
        ));
		$this->template->display('syscontroller_add.html');
    }

	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db->where('id', $id)->limit(1)->get('controller')->row_array();
		if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }
        $url = $this->get_c_url($data);
		$data['url'] = $data['url'] ? $data['url'] : $url;

		if (IS_POST) {
            $data = $this->input->post('data');
            if ($data['url'] == $url) {
                $data['url'] = '';
            }
            $this->db->where('id', $id)->update('controller', $data);
            $this->system_log('修改除自定义控制器【'.$data[1]['type'].'#'.$id.'】'); // 记录日志
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('syscontroller/index'), 1);
		}
		
		$this->template->assign(array(
			'data' => $data,
        ));
		$this->template->display('syscontroller_edit.html');
    }

    // 控制器地址
    public function get_c_url($data) {

        if (!$data['app']) {
            if ($data['type'] == 0) {
                return dr_url($data['cname'].'/index', '', 'index.php');
            } elseif ($data['type'] == 2) {
                return dr_url($data['cname'].'/index');
            }
        } elseif ($data['app'] == 'member') {
            if ($data['type'] == 1) {
                return dr_member_url($data['cname'].'/index');
            } elseif ($data['type'] == 2) {
                return dr_member_url($data['cname'].'/index');
            }
        } elseif ($data['app']) {
            if ($data['type'] == 0) {
                return dr_url($data['app'].'/'.$data['cname'].'/index', '', 'index.php');
            } elseif ($data['type'] == 1) {
                return dr_member_url($data['app'].'/'.$data['cname'].'/index');
            } elseif ($data['type'] == 2) {
                return dr_url($data['app'].'/'.$data['cname'].'/index');
            }
        }

    }

}