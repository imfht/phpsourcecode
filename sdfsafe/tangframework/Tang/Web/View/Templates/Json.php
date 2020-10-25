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
use Tang\Services\ConfigService;

class Json implements ITemplate
{
	public function display($data,&$content)
	{
        $content = json_encode($data);
	}
    public function callback($callback,&$content)
    {
        $content = $callback.'('.$content.');';
    }
    public function __call($method,$parameters)
    {
    }
}