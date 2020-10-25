<?php

namespace api\extensions;

use Yii;
use yii\web\Response;
use yii\base\Behavior;

class ResBeforeSendBehavior extends Behavior
{
    public $defaultCode = 500;

    public $defaultMsg = 'error';

    // 重载events() 使得在事件触发时，调用行为中的一些方法
    public function events()
    {
        // 在 EVENT_BEFORE_SEND 事件触发时，调用成员函数 beforeSend
        return [
            Response::EVENT_BEFORE_SEND => 'beforeSend',
        ];
    }

    // 注意 beforeSend 是行为的成员函数，而不是绑定的类的成员函数。
    // 还要注意，这个函数的签名，要满足事件 handler 的要求。
    public function beforeSend($event)
    {
        try {
            $response = $event->sender;
            if ($response->data === null) {
                $response->data = [
                    'code'  => $this->defaultCode,
                    'msg'   => $this->defaultMsg,
                ];
            } elseif (!$response->isSuccessful) {
                $exception = Yii::$app->getErrorHandler()->exception;
                if (is_object($exception) && !$exception instanceof yii\web\HttpException) {
                    throw $exception;
                } else {
                    $rData = $response->data;
                    $response->data = [
                        'code'  => empty($rData['status']) ? $this->defaultCode : $rData['status'],
                        'msg'   => empty($rData['message']) ? $this->defaultMsg : $rData['message'],
                    ];
                }
            } else {
                /**
                 * $response->isSuccessful 表示是否会抛出异常
                 * 值为 true, 代表返回数据正常，没有抛出异常
                 */
                $rData = $response->data;
                $response->data = [
                    'code' => isset($rData['error_code']) ? $rData['error_code'] : 0,
                    'msg' => isset($rData['res_msg']) ? $rData['res_msg'] : $rData,
                ];
                $response->statusCode = 200;
            }
        } catch (\Exception $e) {
            $response->data = [
                'code'  => $this->defaultCode,
                'msg'   => $this->defaultMsg,
            ];
        }
        return true;
    }
}
