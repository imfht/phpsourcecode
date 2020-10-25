<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 15-11-4
 * Time: 上午8:33
 */
class Setting extends CI_Controller
{
    public $style;
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setting_model');
        $this->style = get_cookie('style')?'bootstrap/'.get_cookie('style'):'bootstrap.min';
        $this->user = $this->session->DMN_USER;
        if ($this->user['level'] <7) {
            show_error('您没有权限查看此内容。');
        }
    }

    public function index()
    {
        $content_filter=$this->db->where('title', 'content_filter')->get('setting')->row_array();
        $filter_t=$filter_c='';
        if ($content_filter) {
            foreach (json_decode($content_filter['value'], true) as $f) {
                if (!isset($f['t'])) {
                    continue;
                }
                $filter_t .= $f['t'] . "\r\n";
                $filter_c .= $f['c'] . "\r\n";
            }
        }
        $data['filter_t']=$filter_t;
        $data['filter_c']=$filter_c;
        $this->load->view('admin/setting', $data);
    }

    public function page()
    {
        $this->load->library('Datatables');
        $search=$this->input->post_get('search');
        if ($search['value']) {
            $this->datatables->where('title', $search['value']);
        }
        $this->datatables->select("*")
            ->where('title <>', 'content_filter')
            ->from('setting');
        echo $this->datatables->generate();
    }

    public function create($id=null)
    {
        $data=array();
        if ($id) {
            $setting=$this->setting_model->get_setting($id);

            $data['setting']=$setting;
        }

        $this->load->view('admin/setting_add', $data);
    }

    public function edit()
    {
        $id=$this->input->post('id')?$this->input->post('id'):0;

        $sql_data=array(
            'title' => $this->input->post('name'),
            'desc' => $this->input->post('desc'),
            'value' => $this->input->post('value')
        );

        if ($id==0) {
            $sql_data['id']=$id;
            $this->db->insert('setting', $sql_data);
        } else {
            $field = $this->input->post('field');
            if ($field) {
                $value = $this->input->post('value');
                $this->db->set($field, $value);
            } else {
                $this->db->set($sql_data);
            }
            $this->db->where('id', $id);
            $this->db->update('setting');
        }
        redirect('admin/setting');
    }

    public function filter()
    {
        $filter_t=explode("\r\n", trim($this->input->post('filter_t')));
        $filter_c=explode("\r\n", $this->input->post('filter_c'));

        $cf=[];
        for ($i=0;$i<count($filter_t);$i++) {
            $cf[]=['t'=>$filter_t[$i],'c'=>$filter_c[$i]];
        }

        $this->db->set('value', json_encode($cf))->where('title', 'content_filter')->update('setting');
        echo '保存成功。';
    }
}
