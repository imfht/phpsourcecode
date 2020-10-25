<?php

class OverloadController extends Controller {

	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		echo 'Index';
	}

	function params()
	{
		echo 'Params:';
		printr($this->input->params(8));

		echo $this->input->get('abc');

		printr(MAGIC_QUOTES_GPC);
	}

	function uriassoc()
	{
		echo 'Global URI';
		printr($GLOBALS['URI']);

		$array = $this->input->assoc(2);

		echo 'Assoc Return';
		printr($array);
	}
	
	function urlseparator()
	{
		echo siteUrl('test/overload/urlspearator/a38');
		printr($GLOBALS['URI']);
	}

	function _overload()
	{
		echo 'true Over load';

		printr($GLOBALS['URI']);
	}

}
