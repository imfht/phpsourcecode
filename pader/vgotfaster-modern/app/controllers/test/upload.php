<?php

class UploadController extends Controller {

	function __construct()
	{
		parent::Controller();
		$this->load->library('upload');
	}

	function index()
	{
		echo '<form action="'.siteUrl('test/upload/doUpload').'" enctype="multipart/form-data" method="post">'
			.'<input type="file" name="testdata" />'
			.'<input type="submit" value="上传" />'
			.'</form>';
	}

	function multiple()
	{
		echo '<form action="'.siteUrl('test/upload/multiUpload').'" enctype="multipart/form-data" method="post">'
			.'<input type="file" name="testdata[a]" /><br />'
			.'<input type="file" name="testdata[b]" /><br />'
			.'<input type="file" name="testdata[c]" /><br />'
			.'<input type="file" name="testdata[d]" /><br />'
			.'One<input type="file" name="filedata" /><br />'
			.'<input type="submit" value="上传" />'
			.'</form>';
	}

	function doUpload()
	{
		$this->upload->initialize(array(
			'uploadTargetPath' => './',
			'overwrite' => FALSE,
			'encryptName' => TRUE,
			'filesizeLimit' => 2048
		));
		$info = $this->upload->doUpload('testdata');
		echo $this->upload->error();
	}

	function multiUpload()
	{
		$this->upload->initialize(array(
			'uploadTargetPath' => './',
			'overwrite' => FALSE,
			'filesizeLimit' => 2048
		));
		$info = $this->upload->doMultiUpload('testdata');
		if($info) {
		foreach($info as $k => $row) {
			if($row !== FALSE) {
				echo $row['origName'].'上传成功<br />';
			} else {
				echo $k.'上传失败<br />';
			}
		}
		} else echo $this->upload->error();
	}

	function ftp()
	{
		echo 'FTP Class<br /><br />';

		$this->load->library('ftp');
		$conn = $this->ftp->connect('localhost','test','test');

		if (!$conn) {
			showError('无法连接 FTP 服务器');
		}

		//$dir = $this->ftp->chdir('folder');

		/*
		$dir = $this->ftp->dir();

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
		*/

		//$this->ftp->upload('changelog.txt','cc.txt');
		$this->ftp->mkdirs('ee/cc',0777);
		$this->ftp->download('test.txt','test.txt');

		$this->ftp->close();
	}

	function __destruct()
	{
		echo '<p>'.round((memory_get_usage() / 1024),2).' KB</p>';
	}

}
