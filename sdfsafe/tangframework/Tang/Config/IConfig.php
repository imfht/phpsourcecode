<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Config;

/**
 * 配置接口
 * @author jibing
 *
 */
interface IConfig
{
	/**
	 * 设置项目路径
	 * @param $directory 项目路径
	 * @return void
	 */
	public function setApplicationDirectory($directory);

	/**
	 * 获取项目路径
	 * @return string
	 */
	public function getApplicationDirectory();
	/**
	 * 获取一个值
	 * @param string $key
	 * @param mixed $defautValue 没有值的时候，获取的默认值
	 */
	public function get($key,$defautValue='');

	/**
	 * 获取一个值 并根据$replaceData替换
	 * @param $key
	 * @param array $replaceData
	 * @return array
	 */
	public function replaceGet($key,array $replaceData);
	/**
	 * 设置一个值
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key,$value);
	/**
	 * 保存所有配置文件
	 */
	public function saveAll();
	/**
	 * 保存$name的配置文件
	 * @param string $name
	 * @param boolean $isCreate
	 */
	public function save($name,$isCreate=false);
	/**
	 * 创建一个配置文件
	 * @param unknown $name
	 */
	public function create($name);
}