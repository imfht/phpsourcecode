<?php

define('CI_DB_BASEPATH', dirname(__FILE__) . '/'); // 数据库文件路径，即放置本套数据库操作类的目录路径。加载文件时需要使用
define('CI_DB_CONFIG_PATH', CI_DB_BASEPATH . 'DB_config.php'); // 数据库配置文件路径
require_once 'DB_Exception.php';

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('CI_DB_BASEPATH') OR exit('No direct script access allowed');

/**
 * Initialize the database
 *
 * @category	Database
 * @author	EllisLab Dev Team
 * @link	https://codeigniter.com/user_guide/database/
 *
 * @param 	string|string[]	$params
 * @param 	bool		$query_builder_override
 *				Determines if query builder should be used or not
 * @return CI_DB_query_builder
 */
function &DB($params = '', $query_builder_override = NULL)
{
    // 如果$params参数不是DSN则加载数据库配置文件
	if (is_string($params) && strpos($params, '://') === FALSE)
	{
	    // 加载配置文件
        if(!file_exists(CI_DB_CONFIG_PATH)) throw new CI_DB_Exception('数据库配置文件 ' . CI_DB_CONFIG_PATH . ' 不存在！');
        require CI_DB_CONFIG_PATH;

        if(!isset($db) OR count($db) === 0) throw new CI_DB_Exception('数据库配置无效！');
        // 将要启用的数据库配置组
        if($params !== '') $active_group = $params;
        if(!isset($active_group)){
            throw new CI_DB_Exception('没有找到数据库配置组名。请确认数据库配置文件内是否有 $active_group 变量');
        } elseif(!isset($db[ $active_group ])){
            throw new CI_DB_Exception('没有找到名为 ' . $active_group . ' 的数据库配置组');
        }

        $params = $db[ $active_group ];
	}
	elseif (is_string($params))
	{
		/**
		 * Parse the URL from the DSN string
		 * Database settings can be passed as discreet
		 * parameters or as a data source name in the first
		 * parameter. DSNs must have this prototype:
		 * $dsn = 'driver://username:password@hostname/database';
		 */
		if (($dsn = @parse_url($params)) === FALSE) throw new CI_DB_Exception('无效的DSN字符串');

		$params = array(
			'dbdriver'	=> $dsn['scheme'],
			'hostname'	=> isset($dsn['host']) ? rawurldecode($dsn['host']) : '',
			'port'		=> isset($dsn['port']) ? rawurldecode($dsn['port']) : '',
			'username'	=> isset($dsn['user']) ? rawurldecode($dsn['user']) : '',
			'password'	=> isset($dsn['pass']) ? rawurldecode($dsn['pass']) : '',
			'database'	=> isset($dsn['path']) ? rawurldecode(substr($dsn['path'], 1)) : ''
		);

        // 设置了额外的配置项目？
		if (isset($dsn['query']))
		{
			parse_str($dsn['query'], $extra);

			foreach ($extra as $key => $val)
			{
				if (is_string($val) && in_array(strtoupper($val), array('TRUE', 'FALSE', 'NULL')))
				{
					$val = var_export($val, TRUE);
				}

				$params[$key] = $val;
			}
		}
	}

    // 没设置数据库驱动？
	if (empty($params['dbdriver'])) throw new CI_DB_Exception('必须设置一个数据库驱动(dbdriver)');

	// Load the DB classes. Note: Since the query builder class is optional
	// we need to dynamically create a class that extends proper parent class
	// based on whether we're using the query builder class or not.
	if ($query_builder_override !== NULL)
	{
		$query_builder = $query_builder_override;
	}
	// Backwards compatibility work-around for keeping the
	// $active_record config variable working. Should be
	// removed in v3.1
	elseif ( ! isset($query_builder) && isset($active_record))
	{
		$query_builder = $active_record;
	}

	require_once(CI_DB_BASEPATH.'DB_driver.php');

    // 根据是否加载查询构造器($query_builder)来基于不同的父类声明CI_DB类
	if ( ! isset($query_builder) OR $query_builder === TRUE)
	{
		require_once(CI_DB_BASEPATH.'DB_query_builder.php');
		if ( ! class_exists('CI_DB', FALSE))
		{
			/**
			 * CI_DB
			 *
			 * Acts as an alias for both CI_DB_driver and CI_DB_query_builder.
			 *
			 * @see	CI_DB_query_builder
			 * @see	CI_DB_driver
			 */
			class CI_DB extends CI_DB_query_builder { }
		}
	}
	elseif ( ! class_exists('CI_DB', FALSE))
	{
		/**
	 	 * @ignore
		 */
		class CI_DB extends CI_DB_driver { }
	}

    // 加载数据库驱动
    $driver_file = CI_DB_BASEPATH . 'drivers/' . $params['dbdriver'] . '/' . $params['dbdriver'] . '_driver.php';
    if(!file_exists($driver_file)) throw new CI_DB_Exception('没有找到 ' . $params['dbdriver'] . ' 数据库驱动');

	require_once($driver_file);

    // 实例化数据库驱动适配器
	$driver = 'CI_DB_'.$params['dbdriver'].'_driver';
	$DB = new $driver($params);

	// Check for a subdriver
	if ( ! empty($DB->subdriver))
	{
		$driver_file = CI_DB_BASEPATH.'drivers/'.$DB->dbdriver.'/subdrivers/'.$DB->dbdriver.'_'.$DB->subdriver.'_driver.php';

		if (file_exists($driver_file))
		{
			require_once($driver_file);
			$driver = 'CI_DB_'.$DB->dbdriver.'_'.$DB->subdriver.'_driver';
			$DB = new $driver($params);
		}
	}

	$DB->initialize();
	return $DB;
}

/**
 * PHP版本对比函数。返回当前环境的PHP版本是否高于或等于$version
 * @param $version
 * @return mixed
 */
function ci_db_is_php($version){
    return version_compare(PHP_VERSION, $version, '>=');
}
/**
 * 记录日志。CI的数据库类中多处调用此函数。于是声明出来，也方便各位收集日志
 * @param string $level   错误等级：'error'、'debug'或'info'
 * @param string $message 错误信息
 */
function ci_db_log_message($level, $message){}