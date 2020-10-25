<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.0.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 * @filesource	svn://www.dayrui.net/v2/news/controllers/category.php
 */

require FCPATH.'dayrui/core/D_Category.php';

class Category extends D_Category {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	
}