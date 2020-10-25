<?php
namespace Thumb;
/**
 * 解析Uri路由  生成对应的配置信息
 * @author seven <397109515@qq.com>
 * @copyright Copyright (c) 2014 seven studio
 */
class Uri{
    
    /**
     * @const string 华丽的分割字符
     */
    const CUT = '_';
    
    /**
     * @const string 华丽的参数分割字符
     */
    const PAR_CUT = '=';
    
    /**
     * @const string 华丽的内容分割字符
     */
    const CONTENT_CUT = '-';
    
    /**
     * @const string Uri信息对象
     */
    protected $string;
    
    /**
     * @const string 返回的结果
     */
    public $uri;
    
    /**
     * 构造方法
     * @param string string 传入的Uri信息
     */
    public function __construct($string) {
        $this->string = $string;
        $this->uri = new \stdClass();
        $this->init();
    }
    
    /**
     * 生成一个配置的数组
     * @return void
     */
    protected function init()
    {
        $uri_arr = explode(self::CUT, $this->string);
        $uri_arr_count = count($uri_arr);
        $filename = explode('.', $uri_arr[0]);
        $this->uri->filename = $filename[0];
        $this->uri->ext = isset($filename[1])?$filename[1]:'';
        for($i=1;$i < $uri_arr_count;$i++)
        {
            $parameter = explode(self::PAR_CUT, $uri_arr[$i]);
            if(isset($parameter[1]))
            {
                $fun_name = 'setConfig'.strtoupper($parameter[0]);
                if(($uri_arr_count-1) == $i)
                {
                    $file_and_ext = explode('.', $parameter[1]);
                    $parameter[1] = isset($file_and_ext[0])?$file_and_ext[0]:$parameter[1];
                }
                if(method_exists($this,$fun_name))
                {
                    $this->$fun_name($parameter[1]);
                }
            }
        }
    }
    
    /**
     * 设置宽度
     * @return void
     */
    protected function setConfigS($string)
    {
        $size = $this->_analysis($string);
        $this->uri->size = array(
            'width'=>(isset($size[0]) and is_numeric($size[0]) and $size[0] > 0)?$size[0]:0,
            'height'=>(isset($size[1]) and is_numeric($size[1]) and $size[1] > 0)?$size[1]:0
        );
    }
    
    /**
     * 设置背景
     * @return void
     */
    protected function setConfigBg($string)
    {
        $this->uri->Bg = $this->_analysis($string);
    }
    
    /**
     * 如果是jpg图片 那么这里就是图片的质量
     * @return void
     */
    protected function setConfigQ($string)
    {
        $this->uri->q = (is_numeric($string))?$string:0;
    }
    
    /**
     * 缩略图 百分比
     * @return void
     */
    protected function setConfigP($string)
    {
        $this->uri->p = (is_numeric($string))?$string:0;
    }
    
    /**
     * 旋转图片
     * @return void
     */
    protected function setConfigR($string)
    {
        $this->uri->r = (is_numeric($string))?$string:0;
    }
    
    /**
     * 截图图片
     * @return void
     */
    protected function setConfigA($string)
    {
        $size = $this->_analysis($string);
        $this->uri->a = array(
            'width'=>(isset($size[0]) and is_numeric($size[0]) and $size[0] > 0)?$size[0]:0,
            'height'=>(isset($size[1]) and is_numeric($size[1]) and $size[1] > 0)?$size[1]:0
        );
    }
    
    /**
     * 缩略图 百分比
     * @return void
     */
    protected function setConfigCc($string)
    {
        $size = $this->_analysis($string);
        $this->uri->cc = array(
            'width'=>(isset($size[0]) and is_numeric($size[0]) and $size[0] > 0)?$size[0]:0,
            'height'=>(isset($size[1]) and is_numeric($size[1]) and $size[1] > 0)?$size[1]:0
        );
    }
    
    /**
     * 局部的宽和高
     * @return void
     */
    protected function setConfigC($string)
    {
        $crop = $this->_analysis($string);
        $this->uri->crop = array(
            (isset($crop[0]) and is_numeric($crop[0]) and $crop[0] > 0)?$crop[0]:0,
            (isset($crop[0]) and is_numeric($crop[0]) and $crop[0] > 0)?$crop[1]:0,
            (isset($crop[0]) and is_numeric($crop[0]) and $crop[0] > 0)?$crop[2]:0,
            (isset($crop[0]) and is_numeric($crop[0]) and $crop[0] > 0)?$crop[3]:0
        );
    }
    
    /**
     * 返回需要加载的图片
     * @return string
     */
    public function getImg()
    {
        return $this->uri->filename.'.'.$this->uri->ext;
    }
    
    /**
     * 解析参数条件 吧参数条件转换成数组
     * @param string string
     * @return array
     */
    protected function _analysis($string)
    {
        return explode(self::CONTENT_CUT, $string);
    }
}