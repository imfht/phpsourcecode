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
namespace Tang\IO\Interfaces;
use Closure;

/**
 * 文件目录接口
 * Interface IDirectory
 * @package Tang\IO\Interfaces
 */
interface IDirectory
{
	/**
	 * 是否是一个文件夹
	 * @param string $path
	 * @return boolean
	 */
	public function isDirectory($path);

	/**
	 * 创建一个文件夹
	 * @param $path
	 * @param int $mode
	 * @return mixed
	 */
	public function create($path,$mode = 0755);

	/**
	 * 删除一个文件夹
	 * @param $path
	 * @return mixed
	 */
	public function delete($path);
	/**
	 * 复制一个文件夹到另外一个文件夹
	 * @param string $sourceDirectory
	 * @param string $destDirectory
	 * @throws DirectoryNotFoundException 源文件夹不存在
	 * @return boolean
	 */
	public function copy($sourceDirectory,$destDirectory);
	/**
	 * 移动$sourceDirectory文件夹到$destDirectory文件夹
	 * @param string $sourceDirectory
	 * @param string $destDirectory
	 * @return boolean
	 */
	public function move($sourceDirectory, $destDirectory);
	/**
	 * 扫描一个文件夹 获得文件内容
	 * @param string $path
	 * @param callback $callback 回调方法或者闭包 将传递IDirectory和SplFileInfo对象
	 * @return boolean
	 */
	public function scan($directory,$callback);
}