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
namespace Tang\Token;
use Tang\Interfaces\ISetConfig;
use Tang\Crypt\Drivers\ICryptDriver;
use Tang\Web\Session\ISession;
/**
 * Token表单令牌接口
 * 用户可根据该接口实现自己的表单验证
 * Interface IToken
 * @package Tang\Token
 */
interface IToken extends ISetConfig
{
    /**
     * 设置令牌的加密驱动
     * @param ICryptDriver $crypt
     * @return mixed
     */
    public function setCrypt(ICryptDriver $crypt);

    /**
     * 设置session
     * @param ISession $session
     * @return mixed
     */
    public function setSession(ISession $session);

    /**
     * 获取session
     * @return ISession
     */
    public function getSession();

    /**
     * 获取加密驱动
     * @return ICryptDriver
     */
    public function getCrypt();
    /**
     * 验证令牌是否正确
     * @param string $name
     * @return boolean
     */
    public function validate($name);

    /**
     * 根据令牌名生成input控件
     * @param string $name
     * @param int $expire 时效（单位秒）
     * @return string
     */
    public function getInput($name,$expire = 0);

    /**
     * 根据令牌名生成令牌值
     * @param string $name
     * @param int $expire 时效（单位秒）
     * @return string
     */
    public function make($name,$expire = 0);
}