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
use Tang\Crypt\Drivers\ICryptDriver;
use Tang\Web\Session\ISession;

/**
 * 令牌实现
 * Class Token
 * @package Tang\Token
 */
class Token implements IToken
{
    /**
     * 加密驱动
     * @var ICryptDriver
     */
    protected $crypt;
    /**
     * session
     * @var ISession
     */
    protected $session;
    /**
     * token有效时间 单位为秒
     * @var int
     */
    protected $expire = 0;
    public function setConfig(array $config)
    {
        //加密时长
        if(isset($config['expire']) && ($config['expire'] = intval($config['expire'])) > 0)
        {
            $this->expire = $config['expire'];
        }
    }

    public function setCrypt(ICryptDriver $crypt)
    {
        $this->crypt = $crypt;
    }

    public function setSession(ISession $session)
    {
        $this->session = $session;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getCrypt()
    {
        return $this->crypt;
    }

    public function validate($name)
    {
        $tokenName = $this->makeTokenName($name);
        $sessionValue = $this->session->get($tokenName);
        if(!$sessionValue || !isset($_REQUEST[$tokenName]) || !$_REQUEST[$tokenName])
        {
            return false;
        }
        $tokenValue = $this->crypt->decode($_REQUEST[$tokenName]);
        $this->session->delete($tokenName);
        return $sessionValue == $tokenValue;
    }

    public function getInput($name,$expire = 0)
    {
        return '<input type="hidden" name="'.$this->makeTokenName($name).'" value="'.$this->make($name,$expire).'">';
    }

    public function make($name,$expire = 0)
    {
        $rand = mt_rand();
        $tokenValue = sha1(uniqid($name.$rand,true));
        $this->session->set($this->makeTokenName($name),$tokenValue);
        return $this->crypt->encode($tokenValue,$expire ? $expire : $this->expire);
    }

    protected function makeTokenName($name)
    {
        return 'tangFramework-'.$name.'-formHash';
    }
}