<?php
/*
 * remedy by wadelau@ufqi.com
 * 2016-05-09
 */
class FileUpload {
	private $filepath; // 指定上传文件保存的路径
	private $allowtype = array (
			'gif',
			'jpg',
			'png',
			'jpeg',
			'doc',
			'txt',
			'xls',
			'zip',
			'7z',
			'gzip',
			'tar.gz',
			'tar',
			'pdf',
			'bmp' 
	); // 充许上传文件的类型
	private $maxsize = 100000000; // 允上传文件的最大长度 1M
	private $israndname = true; // 是否随机重命名， true false不随机，使用原文件名
	private $originName; // 源文件名称
	private $tmpFileName; // 临时文件名
	private $fileType; // 文件类型
	private $fileSize; // 文件大小
	private $newFileName; // 新文件名
	private $errorNum = 0; // 错误号
	private $errorMess = ""; // 用来提供错误报告
	private $newFileNames = array (); // 文件名列表
	private $newFileTypes = array (); // 文件类型列表
	private $newFileSizes = array (); // 文件大小列表
	                               
	// 用于对上传文件初使化
	                               // 1. 指定上传路径， 2，充许的类型， 3，限制大小， 4，是否使用随机文件名称
	                               // 让用户可以不用按位置传参数，后面参数给值不用将前几个参数也提供值
	function __construct($options = array()) {
		foreach ( $options as $key => $val ) {
			$key = strtolower ( $key );
			// 查看用户参数中数组的下标是否和成员属性名相同
			if (! in_array ( $key, get_class_vars ( get_class ( $this ) ) )) {
				continue;
			}
			
			$this->setOption ( $key, $val );
		}
	}
	
	// -
	private function getError() {
		$str = "上传文件<font color='red'>{$this->originName}</font>时出错：";
		
		switch ($this->errorNum) {
			case 4 :
				$str .= "没有文件被上传";
				break;
			case 3 :
				$str .= "文件只被部分上传";
				break;
			case 2 :
				$str .= "上传文件超过了HTML表单中MAX_FILE_SIZE选项指定的值";
				break;
			case 1 :
				$str .= "上传文件超过了php.ini 中upload_max_filesize选项的值";
				break;
			case - 1 :
				$str .= "末充许的类型";
				break;
			case - 2 :
				$str .= "文件过大，上传文件不能超过{$this->maxSize}个字节";
				break;
			case - 3 :
				$str .= "上传失败";
				break;
			case - 4 :
				$str .= "建立存放上传文件目录失败，请重新指定上传目录";
				break;
			case - 5 :
				$str .= "必须指定上传文件的路径";
				break;
			
			default :
				$str .= "末知错误";
		}
		
		return $str . '<br>';
	}
	
	// 用来检查文件上传路径
	private function checkFilePath() {
		if (empty ( $this->filepath )) {
			$this->setOption ( 'errorNum', - 5 );
			return false;
		}
		
		if (! file_exists ( $this->filepath ) || ! is_writable ( $this->filepath )) {
			if (! @mkdir ( $this->filepath, 0755 )) {
				$this->setOption ( 'errorNum', - 4 );
				return false;
			}
		}
		return true;
	}
	
	// 用来检查文件上传的大小
	private function checkFileSize() {
		if ($this->fileSize > $this->maxsize) {
			$this->setOPtion ( 'errorNum', '-2' );
			return false;
		} else {
			return true;
		}
	}
	
	// 用于检查文件上传类型
	private function checkFileType() {
		if (in_array ( strtolower ( $this->fileType ), $this->allowtype )) {
			return true;
		} 
		else {
			$this->setOption ( 'errorNum', - 1 );
			return false;
		}
	}
	
	// 设置上传后的文件名称
	private function setNewFileName() {
		if ($this->israndname) {
			$this->setOption ( 'newFileName', $this->proRandName () );
		} 
		else {
			$this->setOption ( 'newFileName', $this->originName );
		}
	}
	
	// 设置随机文件名称
	private function proRandName() {
		$fileName = date ( "YmdHis" ) . rand ( 100, 999 );
		return $fileName . '.' . $this->fileType;
	}
	private function setOption($key, $val) {
		$this->$key = $val;
	}
	
	// 用来上传一个文件
	function uploadFile($fileField) {
		$return = true;
		// 检查文件上传路径
		if (! $this->checkFilePath ()) {
			$this->errorMess = $this->getError ();
			return false;
		}
		
		$name = $_FILES [$fileField] ['name'];
		$tmp_name = $_FILES [$fileField] ['tmp_name'];
		
		// 防止乱码
		// $name = iconv('utf-8','gb2312',$name);
		// $tmp_name = iconv('utf-8','gb2312',$tmp_name);
		
		$size = $_FILES [$fileField] ['size'];
		$error = $_FILES [$fileField] ['error'];
		
		if (is_Array ( $name )) {
			$errors = array ();
			
			for($i = 0; $i < count ( $name ); $i ++) {
				
				$arrStr = explode ( '.', $name [$i] );
				$this->newFileTypes [] = strtolower ( $arrStr [count ( $arrStr ) - 1] );
				$this->newFileSizes [] = $size [$i];
				
				if ($this->setFiles ( $name [$i], $tmp_name [$i], $size [$i], $error [$i] )) {
					if (! $this->checkFileSize () || ! $this->checkFileType ()) {
						$errors [] = $this->getError ();
						$return = false;
					}
				} 
				else {
					$error [] = $this->getError ();
					$return = false;
				}
				
				if (! $return)
					$this->setFiles ();
			}
			
			if ($return) {
				$fileNames = array ();
				
				for($i = 0; $i < count ( $name ); $i ++) {
					if ($this->setFiles ( $name [$i], $tmp_name [$i], $size [$i], $error [$i] )) {
						$this->setNewFileName ();
						if (! $this->copyFile ()) {
							$errors = $this->getError ();
							$return = false;
						} 
						else {
							$fileNames [] = $this->newFileName;
						}
					}
				}
				
				$this->newFileName = $fileNames;
				$this->newFileNames = $fileNames;
			}
			
			$this->errorMess = $errors;
			return $return;
		} 
		else {
			
			if ($this->setFiles ( $name, $tmp_name, $size, $error )) {
				if ($this->checkFileSize () && $this->checkFileType ()) {
					$this->setNewFileName ();
					if ($this->copyFile ()) {
						return true;
					} 
					else {
						$return = false;
					}
				} 
				else {
					$return = false;
				}
			} 
			else {
				$return = false;
			}
			
			if (! $return){
				$this->errorMess = $this->getError ();
			}
			return $return;
		}
	}
	
	// -
	private function copyFile() {
		if (! $this->errorNum) {
			$filepath = rtrim ( $this->filepath, '/' ) . '/';
			$filepath .= $this->newFileName;
			
			if (@move_uploaded_file ( $this->tmpFileName, $filepath )) {
				return true;
			} 
			else {
				$this->setOption ( 'errorNum', - 3 );
				return false;
			}
		} 
		else {
			return false;
		}
	}
	
	// 设置和$_FILES有关的内容
	private function setFiles($name = "", $tmp_name = '', $size = 0, $error = 0) {
		$this->setOption ( 'errorNum', $error );
		
		if ($error) {
			return false;
		}
		
		$this->setOption ( 'originName', $name );
		$this->setOption ( 'tmpFileName', $tmp_name );
		$arrStr = explode ( '.', $name );
		$this->setOption ( 'fileType', strtolower ( $arrStr [count ( $arrStr ) - 1] ) );
		$this->setOption ( 'fileSize', $size );
		
		return true;
	}
	
	// 用于获取上传后文件的文件路径
	function getFilePath() {
		return $this->filepath;
	}
	
	// 用于获取上传后文件的文件名 =>老的用法保留
	function getNewFileName() {
		return $this->newFileName;
	}
	
	// 用于获取上传后文件的文件名
	function getNewFileNames() {
		return $this->newFileNames;
	}
	
	// 用于获取上传后文件的文件类型
	function getNewFileTypes() {
		return $this->newFileTypes;
	}
	
	// 用于获取上传后文件的文件大小
	function getNewFileSizes() {
		return $this->newFileSizes;
	}
	
	// 上传如果失败，则调用这个方法，就可以查看错误报告
	function getErrorMsg() {
		return $this->errorMess;
	}
}	 