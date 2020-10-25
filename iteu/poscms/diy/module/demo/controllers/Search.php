<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



class Search extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 搜索
     */
    public function index() {
		parent::_search();
    }
}