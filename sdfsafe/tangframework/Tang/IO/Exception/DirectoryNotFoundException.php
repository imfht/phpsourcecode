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
namespace Tang\IO\Exception;
use Tang\Exception\SystemException;

/**
 * 找不到目录异常
 * Class DirectoryNotFoundException
 * @package Tang\IO\Exception
 */
class DirectoryNotFoundException extends SystemException
{
	public function __construct($path,$code)
	{
		parent::__construct('Directory not found',array($path),$code);
	}
}