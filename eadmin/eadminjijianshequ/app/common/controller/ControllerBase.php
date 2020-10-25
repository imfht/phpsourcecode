<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use esclass\Controller;
use app\common\logic\Common as LogicCommon;

/**
 * 系统通用控制器基类
 */
class ControllerBase extends Controller
{

    // 请求参数
    protected $param;

    public static $datalogic;

    // 初始化
    protected function _initialize()
    {

        // 初始化请求信息
        $this->initRequestInfo();

        self::$datalogic = get_sington_object('commonLogic', LogicCommon::class);
    }

    /**
     * 初始化请求信息
     */
    final private function initRequestInfo()
    {

        defined('IS_POST') or define('IS_POST', $this->request->isPost());
        defined('IS_GET') or define('IS_GET', $this->request->isGet());
        defined('IS_AJAX') or define('IS_AJAX', $this->request->isAjax());
        defined('MODULE_NAME') or define('MODULE_NAME', $this->request->module());
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $this->request->controller());
        defined('ACTION_NAME') or define('ACTION_NAME', $this->request->action());
        defined('URL') or define('URL', strtolower($this->request->controller() . SYS_DSS . $this->request->action()));
        defined('URL_MODULE') or define('URL_MODULE', strtolower($this->request->module()) . SYS_DSS . URL);
        defined('CLIENT_IP') or define('CLIENT_IP', $this->request->ip());
        defined('DOMAIN') or define('DOMAIN', $this->request->domain());
        $this->param = $this->request->param();
    }

    /**
     * 系统通用跳转
     */
    final protected function jump($jump_type = null, $message = null, $url = null, $data = null)
    {


        if (is_array($jump_type)):

            switch (count($jump_type)) {
                case 2  :
                    list($jump_type, $message) = $jump_type;
                    break;
                case 3  :
                    list($jump_type, $message, $url) = $jump_type;
                    break;
                case 4  :
                    list($jump_type, $message, $url, $data) = $jump_type;
                    break;
                default :
                    die(RESULT_ERROR);
            }

        endif;

        $success  = RESULT_SUCCESS;
        $error    = RESULT_ERROR;
        $redirect = RESULT_REDIRECT;

        switch ($jump_type) {
            case $success  :
                $this->showstatus($message, $url, $data, $success);
                break;
            case $error    :
                $this->showstatus($message, $url, $data, $error);
                break;
            case $redirect :
                $this->$redirect($url, $message);
                break;
            default        :
                die(RESULT_ERROR);
        }
    }

}
