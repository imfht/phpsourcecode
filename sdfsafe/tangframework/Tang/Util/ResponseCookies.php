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
class ResponseCookies extends Collection
{
    public function insert($name,$value)
    {
        parent::append(new ResponseCookie($name,$value));
    }
    public function __toString()
    {
        return implode('; ',$this->getArrayCopy());
    }
}
/**
 * web client使用的Cookie
 * Class ResponseCookie
 * @package Tang\Util
 */
class ResponseCookie
{
    private $name;
    private $value;
    public function __construct($name,$value)
    {
        $this->name = $name;
        $this->value = $value;
    }
    public function __toString()
    {
        return $this->name.':'.$this->value;
    }
}

