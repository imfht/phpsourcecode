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
namespace Tang\IO;
use Tang\IO\Exception\DirectoryNotFoundException;
use Tang\IO\Interfaces\IDirectory;
use Closure;

/**
 * 文件实现
 * Class Directory
 * @package Tang\IO
 */
class Directory implements IDirectory
{
	/**
     * @see IDirectory::isDirectory
     */
    public function isDirectory($path)
	{
		return is_dir($path);
	}

    /**
     * @see IDirectory::create
     */
	public function create($path,$mode = 0755)
	{
		if(!$this->isDirectory($path))
		{
			return mkdir($path,$mode,true);
		}
		return true;
	}

    /**
     * @see IDirectory::delete
     */
	public function delete($directory)
	{
		if(!$this->isDirectory($directory))
		{
			return false;
		}
		$this->scan($directory,function($directory,$file)
		{
			$path = $file->getPathname();
			if ($file->isDir())
			{
				$directory->delete($path);
			}
			else
			{
				@unlink($path);
			}
		});
		rmdir($directory);
		return true;
	}

    /**
     * @see IDirectory::copy
     */
	public function copy($sourceDirectory,$destDirectory)
	{
		if($this->isDirectory($sourceDirectory))
		{
			throw new DirectoryNotFoundException($sourceDirectory,1221) ;
		}
		$this->create($destDirectory);
		$this->scan($sourceDirectory,function($directory,$file) use($destDirectory)
		{
			$target = $destDirectory.DIRECTORY_SEPARATOR.$file->getBasename();
			$path = $file->getPathname();
			if ($file->isDir() || !$directory->copy($path, $target))
			{
				return false;
			}else if (!copy($path, $target))
			{
				return false;
			}
		});
		return true;
	}

    /**
     * @see IDirectory::move
     */
	public function move($sourceDirectory, $destDirectory)
	{
		if($this->copy($sourceDirectory, $destDirectory))
		{
			return $this->delete($sourceDirectory);
		}
		return false;
	}

    /**
     * @see IDirectory::scan
     */
	public function scan($directory,$callback)
	{
		if(!$this->isDirectory($directory))
		{
			return false;
		}
		$items = new \FilesystemIterator($directory,\FilesystemIterator::SKIP_DOTS);
		foreach ($items as $file)
		{
			call_user_func_array($callback,[$this,$file]);
		}
	}
}