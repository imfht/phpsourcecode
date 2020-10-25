<?php

class MyController extends \Controller {

	public function __construct() {
		parent::__construct();

		header('Content-Type: text/html; charset=utf-8');
	}

}
