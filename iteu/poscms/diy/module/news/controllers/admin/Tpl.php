<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.0.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 * @filesource	svn://www.dayrui.net/v2/member/controllers/admin/tpl.php
 */

require FCPATH.'dayrui/core/D_File.php';

class Tpl extends D_File {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->path = FCPATH.APP_DIR.'/templates/';
		$this->template->assign(array(
			'path' => $this->path,
			'furi' => APP_DIR.'/tpl/',
			'auth' => APP_DIR.'/admin/tpl/',
			'menu' => $this->get_menu(array(
				fc_lang('模板管理') => APP_DIR.'/admin/tpl/index',
				fc_lang('标签向导') => APP_DIR.'/admin/tpl/tag',
			)),
		));
    }
	
}