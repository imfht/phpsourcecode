<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



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
		parent::_index();
    }
}