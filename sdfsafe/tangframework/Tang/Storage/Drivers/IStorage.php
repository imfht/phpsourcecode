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
namespace Tang\Storage\Drivers;
use Tang\Interfaces\ISetConfig;

/**
 * 提供的存储接口，开发者只要实现该接口就可以扩展自己的存储方式
 * Interface IStorage
 * @package Tang\Storage\Drivers
 */
interface IStorage extends ISetConfig
{
    /**
     * 设置当前的bucket
     * @param string $bucket bucket名称
     * @return mixed
     */
    public function setBucket($bucket);

    /**
     * 获取当前bucket的配置信息
     * @return mixed
     */
    public function getBucket();

    /**
     * 在当前的bucket删除$file文件
     * @param $file
     * @return mixed
     */
    public function delete($file);

    /**
     * 在当前的bucket移动$srcFile文件到$destFile
     * 返回的结果为('driver' => '驱动名称','file' =>'路径','bucket' =>'bucket名称','url'=>'外部访问地址');
     * @param $srcFile
     * @param $destFile
     * @return array
     */
    public function move($srcFile,$destFile);

    /**
     * 在当前的bucket复制$srcFile文件到$destFile
     * 返回的结果为('driver' => '驱动名称','file' =>'路径','bucket' =>'bucket名称','url'=>'外部访问地址');
     * @param $srcFile
     * @param $destFile
     * @return array
     */
    public function copy($srcFile,$destFile);

    /**
     * 在当前的bucket读取$file
     * @param $file
     * @return string
     */
    public function read($file);

    /**
     * 在当前的bucket向$file写入$content
     * 返回的结果为('driver' => '驱动名称','file' =>'路径','bucket' =>'bucket名称','url'=>'外部访问地址');
     * @param $file
     * @param $content
     * @return array
     */
    public function write($file,&$content);

    /**
     * 在当前的bucket向$remoteFile写入本地文件$localFile的内容
     * 返回的结果为('driver' => '驱动名称','file' =>'路径','bucket' =>'bucket名称','url'=>'外部访问地址');
     * @param $localFile
     * @param $remoteFile
     * @return array
     */
    public function putFile($localFile,$remoteFile);

    /**
     * 在当前的bucket获取$file的外部访问地址
     * @param $file
     * @return string
     */
    public function getUrl($file);
}