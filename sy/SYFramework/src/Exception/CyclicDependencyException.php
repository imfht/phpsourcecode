<?php
/**
 * 循环依赖
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

class CyclicDependencyException extends Exception implements ContainerExceptionInterface {
}