<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/23
 * Time: 10:37
 */

namespace naples\lib;

/**
 * 本地数组数据库
 * 默认文件存放于 naples/data/arrDatabase下
 */
class ArrData
{
    private $_name;
    private $_filePath;
    public $data=[];

    /**
     * 构造函数
     * @param $name string 数据库名支持多层按/号分割
     */
    public function __construct($name)
    {
        $this->_name=$name;
        $this->_filePath=PATH_NAPLES.'/data/arrDatabase/'.$this->_name;
        $dir=dirname($this->_filePath);
        \Yuri2::createDir($dir);
        if (!is_file($this->_filePath)){
            file_put_contents($this->_filePath,serialize([]));
        }
        $this->load();
    }

    /**
     * 从本地更新数组数据
     */
    public function load(){
        $content=file_get_contents($this->_filePath,LOCK_SH);
        $this->data=unserialize($content);
        if (!is_array($this->data)){
            $this->data=[];
        }
        return $this;
    }

    /**
     * 保存数组数据到本地
     */
    public function save(){
        $dataToSave=serialize($this->data);
//        file_put_contents($this->_filePath,$dataToSave,LOCK_EX);
        \Yuri2::writeData($this->_filePath,'w',$dataToSave);
        return $this;
    }

    /**
     * 删除本地文件
     */
    public function delete(){
        unlink($this->_filePath);
        return $this;
    }

}