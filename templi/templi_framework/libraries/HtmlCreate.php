<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-10-4
 * Time: 下午10:55
 */
namespace framework\libraries;

class HtmlCreate
{
    /**
     * 配置信息
     * @var array
     */
    protected $_config = array(
        'rowStyle'=>'row-style'
    );
    public function __get($name)
    {
        if(isset($this->_config[$name])){
            return $this->_config[$name];
        }
        return null;
    }
    public function __set($name, $val)
    {
        if(isset($this->_config[$name])){
            $this->_config[$name] = $val;
            return true;
        }
        return false;
    }
    /**
     * 行起始
     */
    public function rowStart()
    {
        return '<div class="'.$this->rowStyle.'">';
    }

    /**
     * 行结束
     */
    public function rowEnd()
    {
        return '</div>';
    }
} 