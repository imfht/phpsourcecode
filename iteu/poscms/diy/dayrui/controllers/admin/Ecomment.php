<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



require FCPATH.'branch/fqb/D_Admin_Comment.php';
 
class Ecomment extends D_Admin_Comment {
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->extend(APP_DIR);
	}

}