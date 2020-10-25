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
use Tang\IO\Exception\CanNotWriteException;
use Tang\IO\Exception\FileNotFoundException;
use Tang\IO\Exception\IOException;
use Tang\IO\Exception\CanNotReadException;
use Tang\IO\Interfaces\IFile;
use Tang\Services\DirectoryService;

/**
 * 文件实现
 * Class File
 * @package Tang\IO
 */
class File implements IFile
{
    /**
     * @see IFile::copy
     */
    public function copy($sourceFileName,$destFileName,$overwrite=true)
	{
		if(!$this->exists($sourceFileName))
		{
			throw new FileNotFoundException($sourceFileName);
		}
		if($this->exists($destFileName) && !$overwrite)
		{
			throw new IOException('DestFileName already exists',array($destFileName),1202);
		}
        $this->create($destFileName);
		copy($sourceFileName, $destFileName);
	}

    /**
     * @see IFile::read
     */
	public function read($path, &$content)
	{
		if(!$this->exists($path))
		{
			throw new FileNotFoundException($path);
		} elseif (!$this->isReadable($path))
		{
			throw new CanNotReadException($path, 1203);
		}
		$content = file_get_contents($path);
	}

    /**
     * @see IFile::write
     */
    public function write($path,&$content)
	{
		$this->create($path);
		if (is_array($content))
		{
			$arrayString = "<?php \r\ndefined('IN_CKFRAMEWORK') or die('Access Denied');\r\nreturn " . var_export($content, true) . ";";
			return file_put_contents($path, $arrayString);
		} else
		{
			return file_put_contents($path, $content);
		}
	}

    /**
     * @see IFile::append
     */
	public function append($path,&$content)
	{
		$this->create($path);
		return file_put_contents($path,$content, FILE_APPEND);
	}

    /**
     * @see IFile::create
     */
	public function create($path,$mode=0755)
	{
		if($this->isFile($path) && !$this->isWritable($path) && !$this->chmod($path,$mode))
		{
			throw new CanNotWriteException('Cannot write to the sth file',array($path),1200);
		} else
		{
			DirectoryService::getService()->create(dirname($path));
		}
	}

    /**
     * @see IFile::delete
     */
    public function delete($path)
	{
		if($this->exists($path))
		{
			return @unlink($path);
		}
	}

    /**
     * @see IFile::getExtension
     */
	public function getExtension($path)
	{
		$extension = pathinfo($path, PATHINFO_EXTENSION);
		return strtolower($extension);
	}

    /**
     * @see IFile::getName
     */
	public function getName($path)
	{
		return pathinfo($path, PATHINFO_BASENAME);
	}

    /**
     * @see IFile::isFile
     */
	public function isFile($path)
	{
		return is_file($path);
	}

    /**
     * @see IFile::isWritable
     */
	public function isWritable($path)
	{
		return is_writable($path);
	}

    /**
     * @see IFile::isReadable
     */
	public function isReadable($path)
	{
		return is_readable($path);
	}

    /**
     * @see IFile::chmod
     */
	public function chmod($path,$mode = 777)
	{
		return chmod($path, $mode);
	}

    /**
     * @see IFile::exists
     */
	public function exists($path)
	{
		return file_exists($path);
	}
}