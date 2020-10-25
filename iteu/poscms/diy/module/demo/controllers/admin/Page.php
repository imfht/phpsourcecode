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
		$this->admin_index();
	}
	
	/**
     * 添加
     */
    public function add() {
		$this->admin_add();
	}
	
	
	/**
     * 修改
     */
    public function edit() {
		$this->admin_edit();
	}
	
	/**
     * 缓存
     */
    public function cache() {
		$this->admin_cache();
	}
	
}