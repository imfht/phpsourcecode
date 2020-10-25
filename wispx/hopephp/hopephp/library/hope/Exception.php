<?php

// +----------------------------------------------------------------------
// | HopePHP
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.wispx.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WispX <i@wispx.cn>
// +----------------------------------------------------------------------

namespace hope;

class Exception extends \Exception
{
    /**
     * @var array 保存异常页面显示的额外 Debug 数据
     */
    protected $data = [];

    /**
     * 设置异常额外的 Debug 数据
     * 数据将会显示为下面的格式
     *
     * Exception Data
     * --------------------------------------------------
     * Label 1
     *   key1      value1
     *   key2      value2
     * Label 2
     *   key1      value1
     *   key2      value2
     *
     * @access protected
     * @param  string $label 数据分类，用于异常页面显示
     * @param  array  $data  需要显示的数据，必须为关联数组
     * @return void
     */
    final protected function setData($label, array $data)
    {
        $this->data[$label] = $data;
    }

    /**
     * 获取异常额外 Debug 数据
     * 主要用于输出到异常页面便于调试
     * @access public
     * @return array
     */
    final public function getData()
    {
        return $this->data;
    }

    /**
     * 错误异常构造函数
     * @param integer $severity 错误级别
     * @param string  $message  错误详细信息
     * @param string  $file     出错文件路径
     * @param integer $line     出错行号
     * @param array   $context  错误上下文，会包含错误触发处作用域内所有变量的数组
     */
    public function __construct($severity, $message, $file, $line, array $context = [])
    {
        $this->severity = $severity;
        $this->message  = $message;
        $this->file     = $file;
        $this->line     = $line;
        $this->code     = 0;

        empty($context) || $this->setData('Error Context', $context);
    }
}
