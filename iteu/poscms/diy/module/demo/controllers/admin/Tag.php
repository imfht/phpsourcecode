<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



require FCPATH.'branch/fqb/D_Tag.php';

class Tag extends D_Tag {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	
	public function index() {
		$this->admin_index();
	}
	
	public function add() {
		$this->admin_add();
	}
	
	public function edit() {
		$this->admin_edit();
	}
	
	public function del() {
		$this->admin_del();
	}
	
}