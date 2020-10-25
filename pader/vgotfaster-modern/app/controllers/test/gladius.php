<?php

class GladiusController extends Controller {

	function __construct()
	{
		parent::Controller();
		
		$this->load->library(array('gladius'=>'gladiusdb'));
		$this->load->helper('benchmark');
		
		$this->gladius->setDBRoot(APPLICATION_PATH.'/data/gladius.data/');
	}
	
	function __destruct()
	{
		echo '<p style="border:1px solid #CCC;padding:5px;">'.microtimeUsed().' second(s)<br />'.memeoryUsage().'</p>';
	}
	
	function index()
	{
		echo 'Welcome to gladius database.<br />';
		
		//$this->gladius->SelectDB('myshop') or die($G->errstr);
		
		//$this->gladius->Query('CREATE DATABASE myshop');
		
		filePutContents('aaa.txt','asdfsadfsdf');
		
		//printr($rs->GetArray());

	}

}

?>