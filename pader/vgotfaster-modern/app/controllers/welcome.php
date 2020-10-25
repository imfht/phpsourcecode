<?php
/*
	Default Controller [Demo]
	Only load a view 'welcome_view'
*/

namespace Controller;

class Welcome extends \Controller {

	function index()
	{
		$this->load->helper('benchmark');
		$this->load->view('welcome_view');
	}

}
