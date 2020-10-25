<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Attachment2 extends M_Controller {

    public $type;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->type = array(
            1 => 'FTP',
            2 => '百度云存储BCS',
            3 => '阿里云存储OSS',
            4 => '腾讯云存储COS',
        );
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('远程附件') => array('admin/attachment2/index', 'upload'),
		    fc_lang('添加') => array('admin/attachment2/add', 'plus')
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
            }
			if (!$this->is_auth('admin/attachment2/del')) {
                exit(dr_json(0, fc_lang('您无权限操作')));
            }
            $this->db->where_in('id', $ids)->delete(SITE_ID.'_remote');
			$this->cache(1);
            $this->system_log('删除远程附件配置【#'.@implode(',', $ids).'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}

		$this->template->assign(array(
			'list' => $this->db->get(SITE_ID.'_remote')->result_array(),
		));
		$this->template->display('attachment2_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {

        $error = '';

		if (IS_POST) {
            $data = $this->input->post('data');
            $data['type'] = $this->input->post('type');
            $data['value']['host'] = $data['value']['host'.$data['type']];
            if (!$data['name'] || !$data['url']) {
                $error = fc_lang('名称或地址不能为空');
            } else if (!$data['exts']) {
                $error = fc_lang('扩展名不能为空');
            } else {
                $exts = @explode(',', $data['exts']);
                if (!$exts) {
                    $error = fc_lang('扩展名不能为空');
                } else {
                    foreach ($exts as $e) {
                        if ($e && $row = $this->db->where('`exts` LIKE "%,'.$e.',%"')->get(SITE_ID.'_remote')->row_array()) {
                            $error = fc_lang('扩展名【%s】已经存在于【%s】之中了，确保扩展名的唯一性', $e, $row['name']);
                            break;
                        }
                    }
                    $data['exts'] = ','.implode(',', $exts).',';
                }
            }
            if (!$error) {
                $data['value'] = dr_array2string($data['value']);
                $this->db->insert(SITE_ID.'_remote', $data);
                $this->system_log('添加远程附件配置【#'.$this->db->insert_id().'】'.$data['name']); // 记录日志
                $this->cache(1);
                $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('attachment2/index'), 1);
            }
            $data['exts'] = trim($data['exts'], ',');
		} else {
            $data = array(
                'type' => 1,
                'value' => array(
                    'port' => 21,
                )
            );
        }

        $this->template->assign(array(
            'data' => $data,
            'error' => $error,
        ));
		$this->template->display('attachment2_add.html');
    }

	/**
     * 修改
     */
    public function edit() {

		$id = (int)$this->input->get('id');
		$data = $this->db->where('id', $id)->limit(1)->get(SITE_ID.'_remote')->row_array();
		if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }

		if (IS_POST) {
            $data = $this->input->post('data');
            $data['type'] = $this->input->post('type');
            $data['value']['host'] = $data['value']['host'.$data['type']];
            if (!$data['name'] || !$data['url']) {
                $error = fc_lang('名称或地址不能为空');
            } else if (!$data['exts']) {
                $error = fc_lang('扩展名不能为空');
            } else {
                $exts = @explode(',', $data['exts']);
                if (!$exts) {
                    $error = fc_lang('扩展名不能为空');
                } else {
                    foreach ($exts as $e) {
                        if ($e && $row = $this->db->where('id<>'.$id)->where('`exts` LIKE "%,'.$e.',%"')->get(SITE_ID.'_remote')->row_array()) {
                            $error = fc_lang('扩展名【%s】已经存在于【%s】之中了，确保扩展名的唯一性', $e, $row['name']);
                            break;
                        }
                    }
                    $data['exts'] = ','.implode(',', $exts).',';
                }
            }
            if (!$error) {
                $data['value'] = dr_array2string($data['value']);
                $this->db->where('id', $id)->update(SITE_ID.'_remote', $data);
                $this->system_log('修改远程附件配置【#'.$id.'】'.$data['name']); // 记录日志
                $this->cache(1);
                $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('attachment2/index'), 1);
            }
		} else {
            $data['value'] = dr_string2array($data['value']);
        }
        $data['exts'] = trim($data['exts'], ',');

		$this->template->assign(array(
			'data' => $data,
            'error' => $error,
        ));
		$this->template->display('attachment2_add.html');
    }
	
    /**
     * 缓存
     */
    public function cache($update = 0) {
		$this->system_model->attachment();
		((int)$_GET['admin'] || $update) or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
}