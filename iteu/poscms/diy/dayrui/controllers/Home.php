<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
 
class Home extends M_Controller {

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

        $this->_indexc();
    }

}