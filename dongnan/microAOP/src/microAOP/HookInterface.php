<?php

/**
 * microAOP - 简洁而强大的AOP库
 *
 * @author      Dong Nan <hidongnan@gmail.com>
 * @copyright   (c) Dong Nan http://idongnan.cn All rights reserved.
 * @link        https://github.com/dongnan/microAOP/
 * @license     MIT ( http://mit-license.org/ )
 */

namespace microAOP;

/**
 * HookInterface
 */
interface HookInterface
{

    /**
     * 执行钩子
     * @param mixed $params 参数
     */
    public function run(&$params);
}
