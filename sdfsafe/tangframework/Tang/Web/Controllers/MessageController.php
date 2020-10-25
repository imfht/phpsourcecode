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
namespace Tang\Web\Controllers;
use Tang\Services\ConfigService;
use Tang\Services\I18nService;
use Tang\Services\RequestService;
use Tang\Web\Parameters;
use Tang\Web\View\ViewService;

class MessageController extends WebController
{
	protected $isAjax = true;
    public static function create()
    {
        $instance = new self();
        $instance->request = RequestService::getService();
        $instance->config = ConfigService::getService();
        $instance->i18n = I18nService::getService();
        $instance->setParameters(new Parameters('','',''));
        $instance->view = ViewService::getService();
        return $instance;
    }
    /**
     * 未找到页面
     * @param $message
     */
    public function notFound($message)
    {
        parent::notFound($message);
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
        $message = $this->i18n->get($message);
        parent::message($message,$code,$jumpUrl,$page);
    }
}