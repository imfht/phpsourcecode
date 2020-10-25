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
namespace Tang\Cache\Stores;
use Tang\IO\Interfaces\IFile;
use Tang\Services\DirectoryService;

/**
 * 文件缓存基类
 * 所有使用文件缓存的可继承该类
 * Class FileStore
 * @package Tang\Cache\Stores
 */
abstract class FileStore extends Store
{
    /**
     * 缓存文件夹
     * @var string
     */
    protected $directory;
    /**
     * 文件对象
     * @var \Tang\IO\Interfaces\IFile
     */
    protected $file;
    /**
     * 缓存上级目录
     * @var string
     */
    protected $parentDirectory;

    /**
     * 构造函数
     * @param IFile $file 文件对象
     * @param $directory 目录
     */
    public function __construct(IFile $file,$directory)
	{
		$this->parentDirectory = $directory;
		$type = $this->getType();
		$this->directory = $directory.$type.DIRECTORY_SEPARATOR;
		$this->file = $file;
	}

    /**
     * 获取缓存目录
     * @return string
     */
    public function getDirectory()
	{
		return $this->directory;
	}

    /**
     * 获取缓存上级目录
     * @return string|目录
     */
    public function getParentDirectory()
	{
		return $this->parentDirectory;
	}

    /**
     * 设置缓存目录
     * @param $directory
     */
    public function setDirectory($directory)
	{
		$this->directory = $directory;
	}

    /**
     * (non-PHPdoc)
     * @see IStore::clean()
     */
	public function clean()
	{
		DirectoryService::getService()->delete($this->directory);
	}

    /**
     * (non-PHPdoc)
     * @see IStore::delete()
     */
	public function delete($key)
	{
		$this->file->delete($this->makeFilePath($key));
	}

    /**
     * 构建缓存文件地址
     * @param $name
     * @return string
     */
    protected function makeFilePath($name)
	{
		$md5 = md5($name);
		return $this->directory.substr($md5,0,2).DIRECTORY_SEPARATOR.$md5.'.Tang';
	}

    /**
     * (non-PHPdoc)
     * @see Store::getHandler()
     */
    protected function getHandler($key)
	{
		$filePath = $this->makeFilePath($key);
		$content = '';
        if($this->file->exists($filePath))
        {
            $this->file->read($filePath,$content);
            if(!$content || !($data = $this->serializeHandler($content)))
            {
                return;
            }
        } else
        {
            return;
        }
		if(isset($data['expire']) && ($data['expire'] == 0 || time() < $data['expire']))
		{
			return isset($data['value']) ? $data['value']:null;
		}
		return;
	}

    /**
     * 获取超时时间
     * @param int $expire
     * @return int
     */
    protected function getExpire($expire = 0)
	{
		return $expire>0?$expire+time():0;
	}

    /**
     * 写入缓存
     * @param $key
     * @param $content
     */
    protected function write($key,$content)
	{
		$this->file->write($this->makeFilePath($key),$content);
	}

    /**
     * 格式化处理程序
     * @param $content
     * @return mixed
     */
    protected abstract function serializeHandler($content);

    /**
     * 获取类型
     * @return string
     */
    public abstract function getType();
}