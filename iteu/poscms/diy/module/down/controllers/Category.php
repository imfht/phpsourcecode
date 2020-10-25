<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.0.2
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

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