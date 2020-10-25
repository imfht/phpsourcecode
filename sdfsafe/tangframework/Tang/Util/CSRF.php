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
namespace Tang\Util;
use Tang\Exception\SystemException;
use Tang\Request\IRequest;
use Tang\Web\Cookie\ICookie;
use Tang\Web\Session\ISession;

class CSRF
{
    const NAME = 'TANGCSRF';
    protected $request;
    protected $cookie;
    protected $key;
    protected $encodeValue = '';
    public function __construct(ICookie $cookie)
    {
        $this->cookie  = $cookie;
        $this->key = $this->cookie->get(CSRF::NAME);
        if(!$this->key)
        {
            $this->makeKey();
        }
    }
    public function getValue()
    {
		!$this->encodeValue && $this->encodeValue = md5('TangFramework-'.$this->key.'-csrf');
        return $this->encodeValue;
    }
    public function setRequest(IRequest $request)
    {
        $this->request = $request;
    }
    public function getName()
    {
        return CSRF::NAME;
    }
    public function getInput()
    {
        return '<input type="hidden" name="'.CSRF::NAME.'" k="'.$this->key.'" value="'.$this->getValue().'">';
    }
    public function validate()
    {
        if(!isset($_REQUEST[CSRF::NAME]) || !$_REQUEST[CSRF::NAME] || $_REQUEST[CSRF::NAME] != $this->getValue())
        {
            $this->makeKey();
            $this->request->getResponse()->httpStatus(403);
            throw new CSRFFaildException('The CSRF token could not be verified!'.$this->key);
        }
        return true;
    }

    protected function makeKey()
    {
        $this->key = sha1(uniqid('_CSRF_'.mt_rand(),true));
        $this->cookie->set(CSRF::NAME,$this->key);
    }
}

class CSRFFaildException extends SystemException
{

}