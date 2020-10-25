<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 皮肤控制器
 */
class Skin extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('skin_model');
        $this->load->model('skinsetting_model');
    }

    /**
     * 皮肤列表
     * @param  integer $class      分类,最新new,最热hot
     * @param  integer $page_index 页码,从1开始
     * @param  integer $page_size  每天赠显示数据
     */
    public function index($class = 'new', $page_index = 1, $page_size = 24)
    {
        $this->data['title'] = '换肤';
        $this->data['class'] = $class;

        $this->data['skin_lists'] = $this->skin_model->gets($page_index, $page_size, $class);
        $this->data['skins_count'] = $this->skin_model->gets_count($class);

        //分页
        $config['base_url'] = base_url('skin/' . $class);
        $config['total_rows'] = $this->data['skins_count'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->load->view("{$this->theme_id}/skin", $this->data);
    }

    /**
     * 设置皮肤
     * @param  int $skin_id 皮肤id
     */
    public function setting($skin_id)
    {
        //未登录无法设置皮肤
        $this->is_signin();

        //设置用户使用皮肤
        $this->skinsetting_model->set_user_skin($this->user['id'], $skin_id);
        //皮肤使用量增加1
        $this->skin_model->skin_stats_plus($skin_id);
        //获取设置的皮肤
        $_SESSION['skin'] = $this->skinsetting_model->get_by_userId($this->user['id']);
        redirect();
    }
}
