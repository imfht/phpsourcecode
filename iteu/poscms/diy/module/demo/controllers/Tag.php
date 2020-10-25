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
	
	/**
     * tag列表
     */
    public function index() {
        $this->_tag();
    }
	
}