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
namespace Tang\Request;
/**
 * Cli的Request
 * 为了兼容整体
 * Class CliRequest
 * @package Tang\Request
 */
class CliRequest extends BaseRequest implements IRequest
{
	protected $data;
	public function __construct()
	{
		$this->parseArgs();
	}
	public function isCli()
	{
		return true;
	}
	public function isSsl()
	{
		return false;
	}
	public function get($name,$default='')
	{
		return isset($this->data[$name]) ? $this->data[$name] :$default;
	}
    /**
     * 从标准输入里面获取一行输入数据
     * @return string
     */
    public function readLine()
    {
        return $this->iconv(trim(fgets(STDIN)));
    }
    /**
     * 从标准输入里面获取一个字符
     * @return string
     */
    public function readAChar()
    {
        return $this->iconv(fgetc(STDIN));
    }
    /**
     * 从标准输入里面获取$length长度输入数据
     * @param $length 长度
     * @return string
     */
    public function readByLength($length)
    {
        return $this->iconv(fread(STDIN,$length));
    }
	public function post($name,$default='')
	{
		return null;
	}
	public function put($name,$default='')
	{
		return null;
	}

	public function getHttpMethod()
	{
		return null;
	}
	public function getClientIp()
	{
		return null;
	}
	public function getBrowser()
	{
		return null;
	}
	public function getUrlReferrer()
	{
		return null;
	}
    protected function parseArgs()
    {
        $argv = $_SERVER['argv'];
        array_shift($argv);
        $count = count($argv);
        if($count == 0)
        {
            return;
        } else
        {
            $key = $value = '';
            for($i = 0;$i < $count;$i++)
            {
                if(!isset($argv[$i]))
                {
                    break;
                } else if(strpos($argv[$i],'-') === 0)
                {
                    $key = substr($argv[$i],1);
                    $i++;
                    $value = isset($argv[$i]) ? $argv[$i]:'';
                    $this->data[$key] = $value;
                }
            }
        }
    }
    protected function iconv($string)
    {
        return $this->response->systemToApplicationString($string);
    }
}