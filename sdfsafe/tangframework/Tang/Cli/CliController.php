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
namespace Tang\Cli;
use Tang\Exception\SystemException;
use Tang\Web\Controllers\Controller;

/**
 * Cli控制器基类
 * Class CliController
 * @package Tang\Cli
 */
abstract class CliController extends Controller
{
    protected function invoke($action)
    {
        $notFound = function()
        {
            throw new SystemException('action not found');
        };
        if(!method_exists($this,$action))
        {
            $notFound();
        }
        $ReflectionMethod = new \ReflectionMethod($this,$action);
        if(!$ReflectionMethod->isPublic() || $ReflectionMethod->isStatic())
        {
            $notFound();
        }
        $this->{$action}();
    }

	/**
	 * 未找到页面
	 * @param $message
	 */
	public function notFound($message)
	{
		$this->message($message,404);
	}

	/**
	 * 消息提示
	 * @param $message 消息
	 * @param int $code 错误码 200表示成功
	 * @param string $jumpUrl 跳转页面
	 * @param string $page 消息页面
	 */
	public function message($message,$code=200,$jumpUrl='',$page='message')
	{
		$response = $this->request->getResponse();
		$response->writeLine('Error:'.$message);
		exit;
	}
}