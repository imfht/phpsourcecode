<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 冒泡接口控制器
 */
class Maopao extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('maopao_model');
    }

    /**
     * 添加冒泡
     */
    public function add()
    {
        $maopao_content = $this->input->post('maopao_content');

        //必须有内容
        if (!$this->simplevalidate->required($maopao_content)) {
            $this->result['error_code'] = -200302;
            return;
        }

        //内容长度
        if (!$this->simplevalidate->mix_range($maopao_content, 10, 140)) {
            $this->result['error_code'] = -200303;
            return;
        }

        $maopao = array(
            'maopao_content' => $maopao_content,
            'user_id' => $this->user['id'],
        );

        $maopao = $this->maopao_model->add($maopao);
        if (is_array($maopao)) {
            $this->result['maopao'] = $maopao;
        } else {
            $this->result['error_code'] = -200301;
        }
    }
}
