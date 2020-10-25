<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class MY_Controller 加入一些常用的函数
 */
class MY_Controller extends CI_Controller {
    /**
     * 返回json
     * @param $v mixed 需要返回的数据
     * @param $charset string contentType的编码
     */
    protected function returnJSON($v, $charset='utf-8'){
        $this->output->set_content_type('application/json', $charset);
        $this->output->set_content(json_encode($v));
    }
}