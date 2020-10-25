<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



// 不自动初始化模块
define('DR_IS_SO', 1);
require_once FCPATH.'branch/fqb/D_Module.php';

class Category extends D_Module {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->dir = 'share';

    }

    /**
     * 共享栏目
     */
    public function index() {
        $this->_category(
            (int)$this->input->get('id'),
            $this->input->get('dir', TRUE),
            max(1, (int)$this->input->get('page'))
        );
    }

    /**
     * 生成html
     */
    public function html() {
        $this->_category_html();
    }
}