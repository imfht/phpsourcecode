<?php

/**
 * Dayrui Website Management System
 *
 * @since		version 2.0.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 * @filesource	svn://www.dayrui.net/v2/dayrui/libraries/Dfilter.php
 */

/**
 * 表单数据过滤
 */

class Dfilter {

	private $ci;

	/**
     * 构造函数
     */
    public function __construct() {
		$this->ci = &get_instance();
    }

	/**
	 * 举例测试
	 *
	 * @param   $value	当前字段提交的值
	 * @param   自定义字段参数1
	 * @param   自定义字段参数2
	 * @param   自定义字段参数3 ...
	 * @return  返回处理后的$value值
	 */
	public function __test($value,  $p1) {
		return $value;
	}
}
