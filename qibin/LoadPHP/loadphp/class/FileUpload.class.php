<?php
// +----------------------------------------------------------------------
// | Loadphp Framework designed by www.loadphp.com
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.loadphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 亓斌 <qibin0506@gmail.com>
// +----------------------------------------------------------------------

/**
 +------------------------------------------------------------------------------
 * 文件上传类
 +------------------------------------------------------------------------------
 */
class FileUpload {
    private $filepath;//文件上传路径
    private $allowtype=array('txt','jpg');//允许的上传类型
    private $israndname=true;//是否使用随机文件名
    private $maxsize=2000000;//允许上传的最大字节数
    private $originName;//原始文件名
    private $fileNewName = array();//新文件名
    private $tmpName;//临时文件名
    private $fileType;//文件类型
    private $fileSize;//文件大小
    private $errorNum;//错误编号
    private $errorMsg;//错误信息
    
    // +初始化，用户以数组形式给出，不需要按照一定的顺序排列
    public function __construct($options=array()) {
        foreach($options as $key=>$val) {
            $key = strtolower($key);
            //get_class_vars得到给定类名中的成员属性，get_class得到对象对应的类名
            if(!in_array($key,get_class_vars(get_class($this)))) {
                continue;
            }
            $this->setOptions($key,$val);
        }
    }
    
    // +给相应的成员属性赋值
    private function setOptions($key,$val) {
        $this->$key = $val;
    }
    
    // +配置各错误编号对应的错误信息
    private function getError() {
        $str = "上传{$this->originName}时出错,出错原因:";
        switch($this->errorNum) {
            case 4 : $str .= "没有文件上传";break;
            case 3 : $str .= "文件未上传完整";break;
            case 2 : $str .= "文件大小超出表单设定的值";break;
            case 1 : $str .= "文件大小超出系统设定的值";break;
            case -1 : $str .= "文件大小超出maxsize设置的{$this->maxsize}字节";break;
            case -2 : $str .= "目录创建失败";break;
            case -3 : $str .= "没有指定存放目录";break;
            case -4 : $str .= "不允许的上传类型";break;
            case -5 : $str .= "上传失败";break;
            default : $str .= "未知错误";
        }
        return $str;
    }
    
    // +检查文件类型
    private function checkFileType() {
        if(!in_array(strtolower($this->fileType),$this->allowtype)) {
            $this->setOptions('errorNum',-4);
            return false;
        }
        return true;
    }
    
    // +检查文件大小
    private function checkFileSize() {
        if($this->fileSize > $this->maxsize) {
            $this->setOptions('errorNum',-1);
            return false;
        }
        return true;
    }
    
    // +检查文件上传路径
    private function checkFilePath() {
        if(empty($this->filepath)) {
            $this->setOptions('errorNum',-3);
            return false;
        }
        if(!file_exists($this->filepath) || !is_writable($this->filepath)) {
            if(!@mkdir($this->filepath,0755)) {
                $this->setOptions('errorNum',-2);
                return false;
            }
        }
        return true;
    }
    
    // +设置随机文件名
    private function proRandName() {
        $filename = date("Ymdhis").rand(100,999).".".$this->fileType;
        return $filename;
    }
    
    // +为文件重命名新的文件名
    private function setNewFileName() {
        if($this->israndname) {
            $this->fileNewName[] = $this->proRandName();
        }else {
            $this->fileNewName[] = $this->originName;
        }
    }
    
    // +保存$_FILES[]超全局数组对应的值
    private function setFiles($name,$tmp_name,$size,$error) {
        $this->setOptions('errorNum',$error);
        if($error) {
            return false;
        }
        $this->setOptions('originName',$name);
        $this->setOptions('tmpName',$tmp_name);
        $this->setOptions('fileSize',$size);
        $typeArr = explode('.',$this->originName);
        //strtolower($typeArr[count($typeArr)-1])得到文件类型
        $this->setOptions('fileType',strtolower($typeArr[count($typeArr)-1]));
        return true;
    }
    
    // +移动临时文件到相应的目录
    private function copyFile($witch) {
        if(!$this->errorNum) {//如果$errorNum为0
            $filepath = rtrim($this->filepath,'/').'/';
            $filepath .= $this->fileNewName[$witch];
            if(@move_uploaded_file($this->tmpName,$filepath)) {
                return true;
            }else {
                $this->setOptions('errorNum',-5);
                return false;
            }
        }
    }

    private function fileUpload($name, $tmp_name, $size, $error, $witch) {
        $return = true;
        if($this->setFiles($name,$tmp_name,$size,$error)) {
            if($this->checkFileType() && $this->checkFileSize()) {
                $this->setNewFileName();
                if($this->copyFile($witch)) {
                    return true;
                }else {
                    $return = false;
                }
            }else {
                $return = false;
            }
        }else {
            $return = false;
        }

        if(!$return) {
            $this->errorMsg = $this->getError();
            return false;
        }

        return $return;
    }
    
    // +支持多文件上传
    public function multiUpload($filefield) {
        if(!$this->checkFilePath()) {
            return false;
        }

        $file = $_FILES[$filefield];
        $len = count($file['size']);
        for($i=0;$i<$len;$i++) {
            $name = $file['name'][$i];
            $tmp_name = $file['tmp_name'][$i];
            $size = $file['size'][$i];
            $error = $file['error'][$i];
            if(!$this->fileUpload($name, $tmp_name, $size, $error, $i)) {
                return false;
            }
        }

        return true;
    }

    // +通过本方法实现文件上传
    public function upload($filefield) {
        if(!$this->checkFilePath()) {
            return false;
        }
        $file = $_FILES[$filefield];
        $name = $file['name'];
        $tmp_name = $file['tmp_name'];
        $size = $file['size'];
        $error = $file['error'];
        
        return $this->fileUpload($name, $tmp_name, $size, $error, 0);

    }
    
    // +用户通过本方法得到上传后的文件名
    public function getFileNewName() {
        return $this->fileNewName;
    }
    
    // +用户通过本方法得到上传的错误信息
    public function getErrorMsg() {
        return $this->errorMsg;
    }
}
?>