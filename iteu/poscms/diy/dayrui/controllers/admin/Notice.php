<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 系统提醒
 */
require FCPATH.'branch/fqb/D_Admin_Table.php';

class Notice extends D_Admin_Table {

    public function __construct() {
        parent::__construct();
        $this->mydb = $this->db;
        $this->myid = 'id';
        $this->tfield = 'inputtime';
        $this->mytable = 'admin_notice';

        $this->myfield = array(
            'msg' => array(
                'name' => fc_lang('内容'),
                'ismain' => 1,
                'fieldname' => 'msg',
                'fieldtype' => 'Text',
            ),
        );

        $menu = array(
            fc_lang('处理记录') => array('admin/notice/index', 'volume-down'),
            fc_lang('待处理') => array('admin/notice/my', 'volume-down'),
        );

        if ($this->member['adminid'] == 1) {
            $menu[fc_lang('全部')] = array('admin/notice/all', 'volume-down');
            $this->myfield['username'] = array(
                'name' => fc_lang('会员账号'),
                'ismain' => 1,
                'fieldname' => 'username',
                'fieldtype' => 'Text',
            );
        }

        $this->template->assign(array(
            'menu' => $this->get_menu_v3($menu),
            'field' => $this->myfield,
        ));
    }

    private function delete_all() {

        if (IS_POST && $this->member['adminid'] == 1) {
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            $this->db->where_in('id', $ids)->delete('admin_notice');
            $this->system_log('删除后台提醒【#'.@implode(',', $ids).'】'); // 记录日志
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

    }

    // 我处理的记录
    public function index() {

        $this->mywhere = '`uid`='.$this->uid.'';
        $this->delete_all();
        $this->_index();
        $this->template->display('notice_index.html');
    }

    // 待处理
    public function my() {

        $this->mywhere = '((`to_uid`='.$this->uid.') or (`to_rid`='.$this->member['adminid'].') or (`to_uid`=0 and `to_rid`=0)) and `status`<>3';

        $this->delete_all();
        $this->_index();
        $this->template->display('notice_index.html');
    }

    // 全部记录(管理员看)
    public function all() {

        if ($this->member['adminid'] != 1) {
            $this->index();
            exit;
        }

        $this->mywhere = '';

        $this->delete_all();
        $this->_index();
        $this->template->display('notice_index.html');
    }

    public function go() {

        $id = (int)$this->input->get($this->myid);
        $data = $this->_get_data($id);
        if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }

        // 权限判断
        if ($this->member['adminid'] > 1) {
            if ($data['to_uid'] && $data['to_uid'] != $this->uid) {
                $this->admin_msg(fc_lang('您无权限执行'));
            } elseif ($data['to_rid'] && $data['to_rid'] != $this->member['adminid']) {
                $this->admin_msg(fc_lang('您无权限执行'));
            }
         }

        $url = ADMIN_URL.$this->duri->uri2url($data['uri']);

        if (!$data['status']) {
            $this->db->where('id', $id)->update('admin_notice', array(
                'status' => 1,
                'uid' => $this->uid,
                'username' => $this->member['username'],
            ));
        }

        redirect($url, 'refresh');
    }

}