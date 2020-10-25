<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Downservers extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('下载镜像') => array('admin/downservers/index', 'arrow-circle-down'),
		    fc_lang('添加') => array('admin/downservers/add_js', 'plus'),
		)));
    }
	
	/**
     * 管理
     */
    public function index() {
		if (IS_POST) {
			$ids = $this->input->post('ids', TRUE);
			if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            } elseif (!$this->is_auth('admin/downservers/del')) {
                exit(dr_json(0, fc_lang('您无权限操作')));
            }
            $this->db->where_in('id', $ids)->delete('downservers');
			$this->cache(1);
            $this->system_log('删除镜像下载服务器【#'.@implode(',', $ids).'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}
		$this->template->assign(array(
			'list' => $this->db->order_by('displayorder asc')->get('downservers')->result_array(),
		));
		$this->template->display('downservers_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
		if (IS_POST) {
			$data = $this->input->post('data');
			if (!$data['name'] || !$data['server']) {
                exit(dr_json(0, fc_lang('名称或地址不能为空'), 'name'));
            }
            $data['displayorder'] = (int)$data['displayorder'];
			$this->db->insert('downservers', $data);
            $id = $this->db->insert_id();
            $this->system_log('添加镜像下载服务器【#'.$id.'】'); // 记录日志
			$this->cache(1);
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}
		$this->template->display('downservers_add.html');
    }

	/**
     * 修改
     */
    public function edit() {

		$id = (int)$this->input->get('id');
		$data = $this->db->where('id', $id)->limit(1)->get('downservers')->row_array();
		if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }

		if (IS_POST) {
			$data = $this->input->post('data');
			if (!$data['name'] || !$data['server']) {
                exit(dr_json(0, fc_lang('名称或地址不能为空'), 'name'));
            }
            $data['displayorder'] = (int)$data['displayorder'];
			$this->db->where('id', $id)->update('downservers', $data);
            $this->system_log('修改镜像下载服务器【#'.$id.'】'); // 记录日志
			$this->cache(1);
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}

		$this->template->assign(array(
			'data' => $data,
        ));
		$this->template->display('downservers_add.html');
    }
	
    /**
     * 缓存
     */
    public function cache($update = 0) {
        $this->system_model->downservers();
		((int)$_GET['admin'] || $update) or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
}