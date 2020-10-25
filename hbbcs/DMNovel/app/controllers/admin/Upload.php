<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-8-4
 * Time: 下午2:08
 */
class Upload extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('story_model', 'story');
    }

    function index() {
        $config['upload_path'] = './books/uploads/images'.date('Y/m/');
        $config['allowed_types'] = 'txt';
        $config['max_size'] = 10240;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('story')) {
            show_error($this->upload->display_errors());
        } else {

        }
    }
}