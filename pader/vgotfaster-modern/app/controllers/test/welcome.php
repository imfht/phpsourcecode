<?php
/*
	增加一个注释
*/
class WelcomeController extends Controller {

	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		echo '<p>Welcome to '.anchor('test','test').' Controler</p>';
	}
	
	function GUID()
	{
		$this->load->helper('misc');
		$this->load->database();
		echo getGUID();
	}

	function testredirect()
	{
		echo anchor('test/welcome','Go Index');

	}

	function input()
	{
		echo $this->input->get('aa');
		echo $this->input->ipAddress();
	}

	function ftp()
	{
		$this->load->library('ftp');

		$this->ftp->connect('content.52pk.cc','test','test');

		$this->ftp->chdir('ajaxpost');
		$dir = $this->ftp->dir('chinese');

		echo $this->ftp->current();

		$this->ftp->close();

		if (is_array($dir)) {
			echo "<table border='1'>\r\n";
			echo "<tr><th>目录</th><th>名称</th><th>大小</th><th>修改</th><th>所有权</th><th>组</th><th>属性</th></tr>\r\n";

			foreach ($dir as $row) {
				echo "<tr><td>{$row['isdir']}</td><td>{$row['filename']}</td><td>{$row['filesize']}</td><td>{$row['filemtime']}</td><td>{$row['user']}</td><td>{$row['group']}</td><td>{$row['permission']}</td></tr>\r\n";
			}

			echo '</table>';
		} else {
			exit('目录未找到！');
		}
	}

}
