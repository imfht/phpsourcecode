<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.0.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 * @filesource	svn://www.dayrui.net/v2/news/controllers/search.php
 */

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