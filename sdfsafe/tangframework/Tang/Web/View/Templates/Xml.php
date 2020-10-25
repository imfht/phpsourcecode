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
namespace Tang\Web\View\Templates;
use Tang\Interfaces\IToArray;
use Tang\Util\Format;
use Tang\Web\Parameters;
use Tang\Services\ConfigService;
class Xml implements ITemplate
{
	public function setParameters(Parameters $parameters)
	{
	}
	public function display($data,&$content)
	{
		header('Content-Type:application/xml');
		$content = '<?xml version="1.0" encoding="'.ConfigService::getService()->get('charset').'" ?>';
		$content .= $this->make('',$data);
	}
    public function callback($callback,&$content)
    {
        Format::addslashes($content);
        $content = str_replace("\n","\"+\n\"",$content);
        $content = str_replace("\r","",$content);
        $content = $callback.'("'.$content.'");';
    }
	protected function make($name,$value)
	{
		$content = '';
		if($name !== '')
		{
			$name = is_int($name) ? 'item' : $name;
			$content = '<' .$name. '>';
		}
		if(is_object($value))
		{
            if($value instanceof IToArray)
            {
                $value = $value->toArray();
            } else if($value instanceof \JsonSerializable)
            {
                $value = $value->jsonSerialize();
            }
		}
		if(is_array($value))
		{
            $content .= PHP_EOL;
			foreach ($value as $xmlName => $xmlValue)
			{
				$content .= $this->make($xmlName,$xmlValue);
			}
		} else
		{
			$content .= $value;
		}
		if($name !== '')
		{
			$content .= '</'.$name.'>'.PHP_EOL;
		}
		return $content;
	}
    public function __call($method,$parameters)
    {
    }
}