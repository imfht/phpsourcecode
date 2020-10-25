<?php
class FileUpload{
	private $allowType; //允许上传的文件类型
	private $maxSize;	//允许上传的最大文件大小
	private $filePath;	//上传的路径
	private $newName;	//得到的新文件名
	private $subPath;	//是否生成以日期命名的子目录
	private $extName;	//上传文件的拓展名
	
	function __construct($filePath="./upload",$allowType = array('gif','jpg','jpeg','png'),$maxSize = 10001000,$subPath = true){
		$this->filePath = $filepath;
		$this->allowType = $allowType;
		$this->maxSize = $maxSize;
		$this->subPath = $subPath;
	}
	
	//开始上传文件
	public function startUpload($postname){
		//获取上传的表单名
		if($_FILES["$postname"]['error']){
			die("文件上传失败,错误代码为：".$_FILES[$postname]['error']);
		}
		
		$this->checkSize();
		$this->checkType();
		$this->checkPath();
		$this->createNewName();
		$this->moveFile();
	}
	
	//获取上传后新的文件名
	public function getNewName(){
		return $this->newName;
	}
	
	//获取上传的错误信息
	public function getErrorMsg(){
		
	}
	
	//检查上传文件的大小
	private function checkSize(){
		if($this->maxSize < $_FILES[$postname]['size']){
			die("上传文件大小超出限制值...");
		}
	}
	
	//检查上传文件的类型
	private function checkType(){
		
		if(!in_array(strtolower($this->extName),$this->allowType)){
			die('上传文件的格式不支持...');
		}
	}
	
	//获取上传文件的拓展名
	private function getExtName(){
		$extarr = explode('.',$_FILES[$postname]['name']);
		$this->extName = $extarr[count($extarr)-1];
	}
	
	//检查上传的目录是否存在
	private function checkPath(){
		//检查是否启用子目录
		if($this->subPath){
			$this->filePath = $this->filePath .'/'. date('Ymd').'/';
		}
		//创建目录
		if(!is_dir($this->filePath) && !is_writable($this->filePath)){
			if(!mkdir($this->filepath,0777,true)){
				die("创建目录失败...");
			}
		}
	}
	
	//生成新的文件名
	private function createNewName(){
		$this->newName = $this->filePath . time().rand(10000,99999).$this->extName;
	}
	
	//开始移动上传文件到指定目录
	private function moveFile(){
		//判断临时文件是否存在
		if(!file_exists($_FILES[$postname]['tmp_name'])){
			die('临时文件不存在...');
		}
		//开始移动文件
		if(!move_uploaded_file($_FILES[$postname]['tmp_name'],$this->newName)){
			die('临时文件移动失败...');
		}
	}
}