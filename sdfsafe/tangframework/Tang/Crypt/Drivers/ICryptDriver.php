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
namespace Tang\Crypt\Drivers;
/**
 * 加密接口，开发者需要开发新的加密驱动需要实现该接口
 * Interface ICryptDriver
 * @package Tang\Crypt\Drivers
 */
interface ICryptDriver
{
    /**
     * 加密字符串
     * <code>
     * CryptService::encode('xsdsd',86400);加密xsdsd数据。时效为1天
     * </code>
     * @param string $data 加密的字符串
     * @param int $expire 加密时长 为0 的话永久有效的
     * @return mixed
     */
    public function encode($data,$expire=0);

    /**
     * 解密
     * <code>
     * echo CryptService::decode($data);//如果时效过期，则发货null
     * </code>
     * @param string $data 加密后的字符串
     * @return mixed
     */
    public function decode($data);
}