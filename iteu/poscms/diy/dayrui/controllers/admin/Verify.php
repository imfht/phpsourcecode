<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Verify extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('审核流程') => array('admin/verify/index', 'square'),
		    fc_lang('添加') => array('admin/verify/add_js', 'plus')
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
            $this->db->where_in('id', $ids)->delete('admin_verify');
            $this->system_log('删除审核流程规则【#'.@implode(',', $ids).'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功')));
		}
		
		$this->template->assign('list', $this->auth_model->get_verify_all());
		$this->template->display('verify_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
	
		if (IS_POST) {
		
			$data = $this->input->post('data', TRUE);
            if (count($data['role']) > 8) {
                exit(dr_json(0, fc_lang('最多支持8级审核')));
            }
            $i = 1;
            $role = array();
            foreach ($data['role'] as $t) {
                $role[$i] = $t;
                $i++;
            }
            $data['role'] = $role;
            $this->system_log('添加审核流程规则【'.$data['name'].'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功'), $this->db->insert('admin_verify', array(
				'name' => $data['name'],
				'verify' => dr_array2string($data['role'])
			))));
		}
		
        $role = $this->dcache->get('role');
        $select = '';
        foreach ($role as $t) {
            if ($t['id'] > 1) {
                $select.= '<option value="'.$t['id'].'">'.$t['name'].'</option>';
            }
        }
		
		$this->template->assign(array(
            'role' => $role,
            'select' => $select
        ));
		$this->template->display('verify_add.html');
    }

	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->auth_model->get_verify($id);
		if (!$data) {
            exit(fc_lang('对不起，数据被删除或者查询不存在'));
        }
		
		if (IS_POST) {
		
			$data = $this->input->post('data', TRUE);
		    if (count($data['role']) > 8) {
                exit(dr_json(0, fc_lang('最多支持8级审核')));
            }
            $i = 1;
            $role = array();
            foreach ($data['role'] as $t) {
                $role[$i] = $t;
                $i++;
            }
            $data['role'] = $role;
            $this->system_log('修改审核流程规则【'.$data['name'].'】'); // 记录日志
			
			exit(dr_json(1, fc_lang('操作成功'), $this->db->where('id', $id)->update('admin_verify', array(
				'name' => $data['name'],
				'verify' => dr_array2string($data['role'])
			))));
		}
		
        $role = $this->dcache->get('role');
        $select = '';
        foreach ($role as $t) {
            if ($t['id'] > 1) {
                $select.= '<option value="'.$t['id'].'">'.$t['name'].'</option>';
            }
        }
		
		$this->template->assign(array(
			'data' => $data,
            'role' => $role,
            'select' => $select
        ));
		$this->template->display('verify_add.html');
    }
	
	/**
     * 删除
     */
    public function del() {
        $id = (int)$this->input->get('id');
        $this->db->where('id', $id)->delete('admin_verify');
        $this->system_log('删除审核流程规则【#'.$id.'】'); // 记录日志
		exit(dr_json(1, fc_lang('操作成功')));
	}
    
    /**
     * 流程查看
     */
    public function show() {
        echo '<div style="width:200px;padding-left:90px;padding-bottom:20px">';
        $num = (int)$this->input->get('num');
		for ($i = 1; $i <= $num; $i++) {
            echo '
            <div class="fillet ">'.lang('05'.$i).'</div>
            <div class="fillet-x ">↓</div>
            ';
		}
        echo '<div class="fillet ">'.fc_lang('完成').'</div>';
        echo '</div>';
	}
    
    /**
     * 缓存
     */
    public function cache() {
        $this->system_model->verify();
        (int)$_GET['admin'] or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
}