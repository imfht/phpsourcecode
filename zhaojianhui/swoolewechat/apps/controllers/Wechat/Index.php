<?php

namespace App\Controller\Wechat;

use App\BaseController\WechatBaseController as Base;

class Index extends Base
{
    /**
     * 构造函数，必须申明.
     *
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
    }

    /**
     * 入口文件.
     */
    public function index()
    {
        try {
            //设置Request类，改为调用优化好的的Request，因为swoole不支持原生的file_get_content('php://input');
            $this->easywechat->server->setRequest(\App\Component\Request::createFromGlobals());
            $server = $this->easywechat->server;
            $server->setMessageHandler(function ($message) {
                //触发微信接收消息事件
                \Swoole::$php->event->trigger("WxRecMsg", array('message' => $message));

                $msgType = strtolower($message->MsgType);
                $msgType = ucfirst($msgType);
                $controllerClass = '\\App\\WechatHandler\\' . $msgType;
                $method = 'main'; //主入口方法
                /*if (!class_exists($controllerClass, false)) {
                    throw new Exception('消息类型【' . $message->MsgType . '】处理类不存在');
                }*/
                $controller = new $controllerClass($message);
                if (!method_exists($controller, $method)) {
                    throw new Exception('处理类【' . $controllerClass . '】的[' . $method . ']主入口方法不存在');
                }
                return $controller->$method();
            });
            $response = $server->serve();
            //触发微信发送消息事件
            \Swoole::$php->event->trigger("WxSendMsg", array('message' => $response->getContent()));
            //将响应输出
            $response->send();
        } catch (\Exception $e) {
            if (ENV == 'product'){
                echo 'success';
                $this->log->error($e->getMessage());
            }else{
                throw new \Exception($e->getMessage());
            }
        }
    }
}
