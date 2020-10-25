<?php
/**
 * VgotFaster PHP Framework
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

namespace VF\Library;

/**
 * VgotFaster Upload Library
 *
 * @package VgotFaster
 * @author Pader
 */
class Upload {

	var $set = array();
	var $uploadInfo = array();
	var $errorCode;

	function __construct($set=array())
	{
		$this->set = array(
			/*
				上传文件的表单变量名称
				您也可以在执行 doUpload() 方法时传递第一个字符串以临时更改此值，在 doUpload() 中更改不会在下次生效
			*/
			'fileVarName' => 'filedata',
			'uploadTargetPath' => '.',  //上传到的目标文件夹
			/*
				允许上传的文件格式
				“*” 或代表允许全部格式，多个格式使用 “|”分割
				如： jpg|gif|doc|txt
			*/
			'allowExtensions' => '*',
			/*
				允许上传的文件大小，以 KB 为单位
				若为 0 则不限制
			*/
			'filesizeLimit' => 0,
			/*
				遇到重名文件时是否覆盖
				为 FALSE 时 VF 将自动在文件名后添加 “_1”的数字并依次类推
			*/
			'overwrite' => FALSE,
			/*
				上传后重命名的新名称
				选定的文件将被重命名为此设置的名称，为空时则不重命名，并且只有在 encryptName 为 FALSE 时才生效
			*/
			'rename' => '',
			/*
				重命名时是否重命名整个文件名
				为 FALSE 将保留原后缀，rename项设置了值时此项才有效
			*/
			'overwriteExtension' => TRUE,
			/*
				是否自动重命名文件
				为 TRUE 时，VF将将该文件重命名为一段随机的字符串并保留原格式，当 overwrite 为 FALSE 时才生效
			*/
			'encryptName' => FALSE,
			'removeSpace' => FALSE  //是否使用下划线替换文件名中的空格
		);

		if ($config = getConfig('upload', true)) {
			$this->initialize($config);
		}

		if(count($set) > 0) {
			$this->initialize($set);
		}
	}

	/**
	 * 初始化上传设置
	 *
	 * 为修改系统默认设置为上传做准备
	 *
	 * @param array $set 配置数组
	 * @return void
	 */
	function initialize($set)
	{
		foreach($set as $key => $val) {
			if(isset($this->set[$key])) {
				$this->set[$key] = $val;
			}
		}
		$this->set['uploadTargetPath'] = str_replace('\\','/',$this->set['uploadTargetPath']);
		$this->set['uploadTargetPath'] = rtrim($this->set['uploadTargetPath'],'/');
	}

	/**
	 * 执行单文件上传动作
	 *
	 * @param string|array $fileVarNameOrSet 上传表单名称或配置数组
	 * @param array $set 配置数组
	 * @return array 上传信息
	 */
	function doUpload($fileVarNameOrSet='',$set=array())
	{
		//得到上传文件变量键名 $fileVarName
		$fileVarName = $this->set['fileVarName'];
		if($fileVarNameOrSet) {
			if(is_array($fileVarNameOrSet)) {
				$this->initialize($fileVarNameOrSet);
				$fileVarName = $this->set['fileVarName'];
			} else {
				$fileVarName = $fileVarNameOrSet;
			}
		}

		if(count($set) > 0) {
			$this->initialize($set);
		}

		if(!isset($_FILES[$fileVarName])) {
			$this->errorCode = 'err_no_file';
			return FALSE;
		}

		$this->uploadInfo = $this->progressUpload($_FILES[$fileVarName]);

		return $this->uploadInfo;
	}

	/**
	 * 多文件上传
	 *
	 * @param string|array $fileVarNameOrSet 上传表单名称(不包括"[]"), 或配置数组
	 * @param array $set 配置数组
	 * @return array 上传信息多维数组
	 */
	function doMultiUpload($fileVarNameOrSet='',$set=array())
	{
		//得到上传文件变量键名 $fileVarName
		$fileVarName = $this->set['fileVarName'];
		if($fileVarNameOrSet) {
			if(is_array($fileVarNameOrSet)) {
				$this->initialize($fileVarNameOrSet);
				$fileVarName = $this->set['fileVarName'];
			} else {
				$fileVarName = $fileVarNameOrSet;
			}
		}

		if(count($set) > 0) {
			$this->initialize($set);
		}

		if(!isset($_FILES[$fileVarName])) {
			$this->errorCode = 'err_no_file';
			return FALSE;
		}
		$uploads = $_FILES[$fileVarName];
		$upsInfo = array();
		$isNoFalse = FALSE;
		//循环处理上传过程
		foreach($uploads['name'] as $key => $name) {
			$upload = array();
			foreach(array('name','type','tmp_name','error','size') as $r) {
				$upload[$r] = $uploads[$r][$key];
			}
			$upsInfo[$key] = $this->progressUpload($upload,TRUE);
			if($upsInfo[$key] !== FALSE) $isNoFalse = TRUE;
		}

		if($isNoFalse) {
			$this->uploadInfo = $upsInfo;
			return $this->uploadInfo;
		} return FALSE;
	}

	/**
	 * 处理上传的文件
	 *
	 * 处理上传的文件，由单文件上传 doUpload() 和多文件上传 doMultiUpload() 调用
	 * 也可以在控制器中调用等，但并不推荐
	 *
	 * @param array $upload 上传数组
	 * @param bool $multiple 预留
	 * @return array 上传信息
	 */
	function progressUpload($upload,$multiple=FALSE)
	{
		$upInfo = array();

		$uploadSource = str_replace('\\\\','\\',$upload['tmp_name']);

		$this->errorCode = $upload['error'];
		if($upload['error'] != 0) {
			return FALSE;
		}

		if(!is_uploaded_file($uploadSource)) {
			$this->errorCode = 'err_no_file';
			return FALSE;
		}

		//文件大小检查
		if($this->set['filesizeLimit'] > 0) {
			if($upload['size'] / 1024 > $this->set['filesizeLimit']) {
				$this->errorCode = 'err_allow_size';
				return FALSE;
			}
		}

		$upInfo['fileType'] = $upload['type'];
		$upInfo['origName'] = $upload['name'];

		$extension = $this->pathinfo($upload['name'],'extension');

		//allowExtensions 选项
		if(!$this->checkExtension($extension)) {
			$this->errorCode = 'err_allow_ext';  //文件格式不允许
			return FALSE;
		}

		//rename 选项,只对单文件上传有效
		if(!empty($this->set['rename']) and $this->set['encryptName'] == FALSE) {
			if($this->set['overwriteExtension']) {  //overwriteExtension
				$upload['name'] = $this->set['rename'];
			} else {
				$upload['name'] = $this->set['rename'].(empty($extension) ? '' : '.'.$extension);
			}
		}

		//overwrite 选项
		$fileUpTarget = $this->set['uploadTargetPath'].'/'.$upload['name'];
		if($this->set['overwrite'] == FALSE) {
			$fileUpTarget = $this->getOverwritePath($fileUpTarget);
		}

		//removeSpace 选项
		if($this->set['removeSpace']) {
			$fileUpTarget = str_replace(' ','_',$fileUpTarget);
		}

		if(!@move_uploaded_file($uploadSource,$fileUpTarget)) {
			if($this->errorCode == 0) {
				$this->errorCode = 'err_cant_write';
			}
			return FALSE;
		}

		$this->errorCode = 'upload_successfuly';
		$pathinfo = $this->pathinfo($fileUpTarget);

		$upInfo['fullPath'] = realpath($fileUpTarget);
		$upInfo['fileName'] = $pathinfo['basename'];
		$upInfo['fileExt'] = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
		$upInfo['fileSize'] = $upload['size'];

		return $upInfo;
	}

	/**
	 * 检查文件格式是否允许上传
	 *
	 * @param string $extension
	 * @return bool
	 */
	private function checkExtension($extension)
	{
		if($this->set['allowExtensions'] != '*' and !empty($extension)) {
			$allowExtensions = explode('|',$this->set['allowExtensions']);
			$allowExtensions = array_map('trim',$allowExtensions);
			$extension = strtolower($extension);
			return in_array($extension,$allowExtensions);
		}
		return TRUE;
	}

	/**
	 * 获取一个不重名的上传路径
	 *
	 * 在 overwrite 为 FALSE 并且文件有重名时，获取可以使用的文件名
	 * 自动在后面加数字
	 *
	 * @param string $fileUpTarget
	 * @return string 新的上传路径
	 */
	private function getOverwritePath($fileUpTarget)
	{
		if(!file_exists($fileUpTarget) and !$this->set['encryptName']) {
			return $fileUpTarget;
		}
		$pathinfo = $this->pathinfo($fileUpTarget);
		$ext = isset($pathinfo['extension']) ? '.'.$pathinfo['extension'] : '';
		$fileHash = uniqid();

		for($i=0;;$i++) {
			//encryptName 选项
			$fileName = $this->set['encryptName'] == TRUE ? str_shuffle($fileHash) : $pathinfo['filename']."_$i";
			$fileUpTarget = $pathinfo['dirname'].'/'.$fileName.$ext;
			if(!file_exists($fileUpTarget)) {
				return $fileUpTarget;
			}
		}
	}

	/**
	 * 显示错误
	 *
	 * @return
	 */
	function error()
	{
		/*
			UPLOAD_ERR_OK = 0，没有错误发生，文件上传成功。
			UPLOAD_ERR_INI_SIZE = 1，上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。
			UPLOAD_ERR_FORM_SIZE = 2，上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。
			UPLOAD_ERR_PARTIAL = 3，文件只有部分被上传。
			UPLOAD_ERR_NO_FILE = 4，没有文件被上传。
			UPLOAD_ERR_NO_TMP_DIR = 6，找不到临时文件夹。PHP 4.3.10 和 PHP 5.0.3 引进。
			UPLOAD_ERR_CANT_WRITE = 7，文件写入失败。PHP 5.1.0 引进。
		*/
		$VF =& getInstance();
		$lang = $VF->config->lang('upload');

		if(is_numeric($this->errorCode)) {
			$errorCodes = array(
				0 => 'upload_successfuly',
				1 => 'err_ini_size',
				2 => 'err_form_size',
				3 => 'err_partial',
				4 => 'err_no_file',
				6 => 'err_no_tmp_dir',
				7 => 'err_cant_write',
				8 => 'err_allow_ext'  //custom
			);
			if(isset($errorCodes[$this->errorCode])) {
				$this->errorCode = $errorCodes[$this->errorCode];
			}
		}

		return isset($lang[$this->errorCode]) ? $lang[$this->errorCode] : $lang['err_unknow'];
	}

	/**
	 * 此函数用于取代 php 自带的 pathinfo() 函数
	 *
	 * 自带的 pathinfo() 函数在非 windows 操作系统中表现很不正常
	 *
	 * @param string $path 文件路径
	 * @param string $get 是否只获取返回值中的一个元素[填写索引]
	 * @return array|string
	 */
	function pathinfo($path,$get='')
	{
		$path = str_replace('\\','/',$path); //只使用 / 斜杠
		$info = array();

		$pathExport = explode('/',$path);
		$count = count($pathExport);
		$baseName = end($pathExport);

		$lastPoint = strrpos($baseName,'.');
		unset($pathExport[$count - 1]);

		$info['dirname'] = join('/',$pathExport);
		$info['basename'] = $baseName;  //baseName
		$info['extension'] = $lastPoint !== FALSE ? substr($baseName,$lastPoint + 1) : '';  //extension
		$info['filename'] = $lastPoint !== FALSE ? substr($baseName,0,$lastPoint) : $info['basename'];  //fileName
		return empty($get) ? $info : $info[$get];
	}

}
