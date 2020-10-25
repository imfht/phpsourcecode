<?php

require FCPATH.'branch/fqb/D_Form.php';

class Form_liuyan extends D_Form {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->_post();
	}

}