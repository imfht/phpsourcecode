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
namespace Tang\Response;
/**
 * Class CliResponse
 * @package Tang\Response
 */
class CliResponse implements IResponse
{
    /**
     * 系统编码
     * @var string
     */
    protected $systemCharset = '';
    /**
     * 应用编码
     * @var string
     */
    protected $applicationCharset = '';
    /**
     * 是否需要转码
     * @var bool
     */
    protected $havaIconv = false;
    public function __construct($charset)
    {
        //没有服务机做测试，只能先探测windows和linux.其他的待BUG提交上来进行修改
        switch(PHP_OS)
        {
            case 'WINNT':
            case 'WIN32':
            case 'Windows':
                $this->systemCharset = 'gbk';
                break;
            default:
                $this->systemCharset = 'utf-8';
        }
        $this->applicationCharset = strtolower($charset);
        $this->havaIconv = $this->systemCharset != $this->applicationCharset;
    }

    /**
     * @see IResponse::write
     */
    public function write($string,Array $args=array())
    {
        $args && $string = vsprintf($string, $args);
        if($this->havaIconv)
        {
            $string = iconv($this->applicationCharset,$this->systemCharset.'//IGNORE', $string);
        }
        echo $string;
    }

    /**
     * @see IResponse::writeArray
     */
    public function writeArray($array)
    {
        $this->write(var_export($array,true));
    }

    /**
     * @see IResponse::writeLine
     */
    public function writeLine($string)
    {
        $this->write($string.PHP_EOL);
    }

    /**
     * 将字符串由系统编码转换为应用编码
     * @param $string
     * @return string
     */
    public function systemToApplicationString($string)
    {
        if($this->havaIconv)
        {
            $string = iconv($this->systemCharset,$this->applicationCharset.'//IGNORE', $string);
        }
        return $string;
    }
}