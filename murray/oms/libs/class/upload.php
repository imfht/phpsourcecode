<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 上传类
*/

defined('INPOP') or exit('Access Denied');

class upload{
	public $tmp_name;
    public $file_name;
    public $file_size;
    public $file_type;
	public $file_error;
    public $savename;
    public $savepath;
	public $saveto;
    public $fileformat = UPLOAD_FORMAT;
    public $overwrite = 0;
    public $maxsize = 0;
    public $ext;
    public $errname;

	public function __construct(){}

	//初始化
    public function init($fileArr, $savename, $savepath, $overwrite = 0, $maxsize = 0){
        $this->tmp_name = $fileArr['tmp_name'];
        $this->file_name = $fileArr['name'];
        $this->file_size = $fileArr['size'];
        $this->file_type = $fileArr['type'];
		$this->file_error = $fileArr['error'];

        $this->getExt();
        $this->setSavepath($savepath);
        $this->setOverwrite($overwrite);
        $this->setSavename($savename);
        $this->setMaxsize($maxsize);
    }

	//上传
    public function up(){
		if($this->file_error == UPLOAD_ERR_PARTIAL || $this->file_error == UPLOAD_ERR_NO_FILE ){
			$this->errname = 'the_filesize_is_over';
			return false;
		}

        if ($this->file_error == UPLOAD_ERR_INI_SIZE || $this->file_error == UPLOAD_ERR_FORM_SIZE || ($this->maxsize > 0 && $this->file_size > $this->maxsize)){
             $this->errname = 'too_large';
             return false;
        }

        if (!$this->validateFormat()){
            $this->errname = 'not_allowed_such_type';
            return false;
        }
        if(!@is_writable(UPLOAD_DIR.DS.$this->savepath)){
            $this->errname = 'not_writeable';
            return false;
        }
        if($this->overwrite == 0 && @file_exists($this->saveto)){
            $this->errname = 'file_existed';
            return false;
        }
        if(!$this->uploadfile($this->tmp_name, UPLOAD_DIR.DS.$this->saveto)){
            $this->errname = 'can_not_find_temp_directory';
            return false;
        }
	    $this->errname = 'file_upload_success';
		chmod(UPLOAD_DIR.DS.$this->saveto, 0777);
        return true;
    }

	//转移文件
	public function uploadfile($file, $saveto){
		return function_exists("move_uploaded_file") ? move_uploaded_file($file, $saveto) : copy($file, $saveto);
	}

	//验证格式
    public function validateFormat(){
        return $this->fileformat && preg_match("/^(".$this->fileformat.")$/i",$this->ext) && !preg_match("/^(php|php3|php4)$/i",$this->ext);
    }

	//获取后缀
    public function getExt(){
        $this->ext = strtolower(trim(substr(strrchr($this->file_name, '.'), 1)));
    }

	//获取文件大小
    public function setMaxsize($maxsize){
        $this->maxsize = intval($maxsize);
    }

	//是否覆盖
    public function setOverwrite($overwrite=1){
        $this->overwrite = intval($overwrite) == 1 ? 1 : 0;
    }

	//设置格式
    public function setFileformat($fileformat){
        $this->fileformat = $fileformat;
    }

	//设置保存路径
    public function setSavepath($savepath){
		$savepath = str_replace("\\", "/", $savepath);
        $this->savepath = $savepath;
    }

	//自动设置文件名字
    public function setSavename($savename){
        if(!$savename){
            srand ((double) microtime() * 1000000);
            $name = date('Ymdhis').rand(100,999);
            $this->savename = $name.".".$this->ext;
        }else{
            $this->savename = $savename.".".$this->ext;
        }
		$this->saveto = $this->savepath.$this->savename;
    }

	//出错显示
    public function errMsg(){
         return $this->errname;
    }
}
?>