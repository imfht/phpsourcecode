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
/**
 * 文件操作接口
 * Interface IFile
 * @package Tang\IO\Interfaces
 */
interface IFile
{
    /**
     * 复制文件 如果$overwrite为true的话则会覆盖$destFileName
     * @param $sourceFileName
     * @param $destFileName
     * @param bool $overwrite
     * @return mixed
     */
    public function copy($sourceFileName,$destFileName,$overwrite=true);

    /**
     * 读取文件内容
     * @param string $path 读取的文件路径
     * @param string $content 引用文件内容，
     * @throws FileNotFoundException 未找到文件异常
     * @throws CanNotReadException 不能读异常
     */
	public function read($path, &$content);

    /**
     * 写入内容到文件
     * @param $path
     * @param $content
     * @return int
     */
	public function write($path,&$content);

    /**
     * 向文件后添加数据
     * @param string $path
     * @param string $content
     * @return number
     */
	public function append($path,&$content);

    /**
     * 创建文件
     * @param string $path
     * @param int $mode
     */
	public function create($path,$mode=0755);

    /**
     * 删除文件
     * @param $path
     * @return bool
     */
    public function delete($path);

    /**
     * 获取文件后缀名
     * @param string $path
     * @return mixed
     */
    public function getExtension($path);

    /**
     * 获取文件名
     * @param string $path
     * @return mixed
     */
    public function getName($path);

    /**
     *
     * 是否是一个文件
     * @param string $path
     * @return boolean
     */
    public function isFile($path);

    /**
     * 判断文件是否可写
     * @param string $path
     * @return boolean
     */
    public function isWritable($path);

    /**
     * 判断文件是否可读
     * @param string $path
     * @return boolean
     */
    public function isReadable($path);

    /**
     * 给文件设置权限
     * @param $path
     * @param  $mode
     * @return boolean
     */
    public function chmod($path,$mode = 777);

    /**
     * 判断文件是否存在
     * @param string $path
     * @return boolean
     */
    public function exists($path);
}