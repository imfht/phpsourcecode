<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



require FCPATH.'branch/fqb/D_Member_Home.php';
 
class Home extends D_Member_Home {
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	
}