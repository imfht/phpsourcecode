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
use Tang\Web\Parameters;

/**
 * Rest控制器
 * Class RestController
 * @package Tang\Web\Controllers
 */
class RestController extends WebController
{
    protected $isAjax = true;

    /**
     * @see Controller::setParameters
     */
    protected function setParameters(Parameters $parameters)
    {

        $ajaxType = $this->request->getRouter()->getExtension();
        if($ajaxType)
        {
            $ajaxType = strtolower($ajaxType);
        } else
        {
            $ajaxType = 'html';
        }
        $parameters->setViewType($ajaxType);
        Controller::setParameters($parameters);
    }
}