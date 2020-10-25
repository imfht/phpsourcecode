<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


 
require FCPATH.'branch/fqb/D_Page.php';

class Page extends D_Page {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 首页
     */
    public function index() {
		$this->_page();
    }

}