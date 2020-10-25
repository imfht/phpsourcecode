<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.0.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 * @filesource	svn://www.dayrui.net/v2/dayrui/controllers/admin/page.php
 */

require FCPATH.'dayrui/core/D_Page.php';

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