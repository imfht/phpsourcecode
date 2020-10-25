<?php

class DownloadController extends Controller {

	public function __construct()
	{
		parent::Controller();
		$this->load->helper('download');
	}

	function index()
	{
		echo '<title>обть╨╞йЩ╡Бйт</title>';
		echo anchor('test/download/bigfile','обть╡Бйт');
	}

	function trydownload()
	{
		headerDownload(__FILE__);
	}

	function bigfile()
	{
		forceDownload('cs.rar');
	}

}
