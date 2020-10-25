<?php

/**
 * 通用错误控制器
 * Date: 16-10-18
 * Time: 下午9:54
 * author :李华 yehong0000@163.com
 */

namespace system\controllers;

use Yaf\Registry;
use \Yaf\Dispatcher;

class Error extends \Yaf\Controller_Abstract {
    public function init() {
        Dispatcher::getInstance()->disableView();
        $this->setViewpath(VIEW_DIR);
    }

    public function errorAction($exception) {
        if (Registry::get('config')->debug) {
            var_dump($exception);
            return 0;
        }
        $Response = $this->getResponse();
        $Request = $this->getRequest();
        $Response->clearBody();
        if ($Request->isGet()) {//get请求时返回
            if (in_array($exception->getCode(), [512, 513, 514, 519, 520, 521])) {
                $Response->setHeader($Request->getServer('SERVER_PROTOCOL'), '500 Internal Server Error');
                $this->getView()->assign('message', $exception->getMessage());
                $this->display('error');
            } elseif (in_array($exception->getCode(), [515, 516, 517, 518])) {
                $Response->setHeader($Request->getServer('SERVER_PROTOCOL'), '404 Not Found');
                $this->getView()->assign('message', '页面不存在!');
                $this->display('404');
            } else {
                $Response->setHeader('Content-Type', 'application/json; charset=utf-8');
                $Response->setHeader($Request->getServer('SERVER_PROTOCOL'), '503 Service Unavailable');
                $Response->setBody($this->packing($exception->getCode(), '不存在的api地址!', null, null, null));
            }
        } else {
            $Response->setHeader('Content-Type', 'application/json; charset=utf-8');
            $Response->setBody($this->packing($exception->getCode(), '系统错误！', null, null, null));
        }
    }

    /**
     * 返回消息打包格式化
     *
     * @param int $status
     * @param $msg
     * @param $data
     * @param string $type
     * @param $rootNodeName
     */
    function packing($status = 0, $msg, $data, $type = 'json', $rootNodeName) {
        $returnData = array(
            'code' => $status,
            'msg'  => $msg,
            'data' => $data
        );
        if ($type == 'json' || !$type) {
            return json_encode($returnData, JSON_UNESCAPED_UNICODE);
        } elseif ($type == 'xml') {
            $rootNodeName = $rootNodeName ?: 'root';
            return Tool::arrToXml($returnData, $rootNodeName, null);
        }
    }
}