<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



class Category extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	
	/**
     * 栏目列表
     */
    public function index() {
        $this->_category((int)$this->input->get('id'), $this->input->get('dir', TRUE), max(1, (int)$this->input->get('page')));
    }
	
	/**
     * 生成html
     */
    public function html() {
        $this->_category_html();
    }
	
}