<?php
/**
 * 尝试创建无效的类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Exception
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\Exception;

use Psr\Container\ContainerExceptionInterface;

class InvalidClassException extends Exception implements ContainerExceptionInterface {
}