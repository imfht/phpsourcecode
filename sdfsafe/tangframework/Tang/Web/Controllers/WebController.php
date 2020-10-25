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
use Tang\Web\Parameters;
use Tang\Web\View\ViewService;

/**
 * Web控制器
 * Class WebController
 * @package Tang\Web\Controllers
 */
class WebController extends Controller
{
    /**
     * 不允许请求的方法
     * @var array
     */
    protected $notAllowFunctions = array();
    /**
     * @var \Tang\Web\View\IView
     */
    protected $view;
    /**
     * 是否AJAX运行
     * @var bool
     */
    protected $isAjax = false;

    /**
     * @see Controller::invoke
     */
    protected function invoke($action)
    {
        $this->setView();
        if(!$this->view)
        {
            $this->view = ViewService::getService();
        }
        $notFound = function(WebController $controller)
        {
            $controller->notFound($this->i18n->get('action not found'));
        };
        if(!method_exists($this,$action) || in_array($action,$this->notAllowFunctions))
        {
            $notFound($this);
        }
        $reflectionMethod = new \ReflectionMethod($this,$action);
        if(!$reflectionMethod->isPublic() || $reflectionMethod->isStatic())
        {
            $notFound($this);
        }
        $this->endInvoke();
        $this->{$action}();
    }

    /**
     * @see Controller::setParameters
     */
    protected function setParameters(Parameters $parameters)
    {
        $ajax = $this->config->get('ajax');
        $ajaxType = $this->request->get($ajax['requestName']);
        if($ajaxType)
        {
            $ajaxType = strtolower($ajaxType);
        } else
        {
            //判断后缀名
            $ajaxType = $this->request->getRouter()->getExtension();
            if($ajaxType)
            {
                $ajaxType = strtolower($ajaxType);
            } else
            {
                $ajaxType = 'html';
            }
        }
        $parameters->setViewType($ajaxType);
        parent::setParameters($parameters);
    }

    /**
     * 显示输出代码
     * @param string $template
     * @param string $saveFilePath
     * @param bool $isOutput
     * @return mixed
     */
    protected function display($template='',$saveFilePath = '',$isOutput = true)
    {
        $viewType = '';
        if(!$this->isAjax)
        {
            $viewType = 'html';
        } else
        {
            $viewType = $this->parameters->viewType;
        }
        if(!$this->view->get('statusCode'))
        {
            $this->view->assgin('statusCode',200);
        }
        return $this->view->display($this->parameters,$viewType,$template,$saveFilePath,$isOutput);
    }

    /**
     * 留给子类设置视图
     */
    protected function setView()
    {
    }

    /**
     * 留给子类进行设置
     */
    protected function endInvoke()
    {
    }

    /**
     * 未找到页面
     * @param $message
     */
    protected function notFound($message)
    {
        $response = $this->request->getResponse();
        $response->httpStatus(404);
        $this->message($message,404,'','404');
    }

    /**
     * 消息提示
     * @param $message 消息
     * @param int $code 错误码 200表示成功
     * @param string $jumpUrl 跳转页面
     * @param string $page 消息页面
     */
    protected function message($message,$code=200,$jumpUrl='',$page='message')
    {
        $response = $this->request->getResponse();
        $response->noCache();
        $this->view->assgin('message',$message);
        $this->view->assgin('statusCode',$code);
        $this->view->assgin('jumpUrl',$jumpUrl);
        $templateFile = ucfirst($this->config->get($page.'Page',$page));
        $templateFilePath = $this->config->get('applicationDirectory').'Lib'.DIRECTORY_SEPARATOR.'Pages'.DIRECTORY_SEPARATOR.$templateFile;
        if(!file_exists($templateFilePath))
        {
            $templateFilePath = $this->config->get('frameworkDirectory').'Pages'.DIRECTORY_SEPARATOR.$templateFile;
        }
        $this->display($templateFilePath);
        exit();
    }
}