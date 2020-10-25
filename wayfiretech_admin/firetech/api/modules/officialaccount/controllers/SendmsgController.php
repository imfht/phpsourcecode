<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-05 08:26:29
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-07 10:18:15
 */

namespace api\modules\officialaccount\controllers;

use api\controllers\AController;
use backend\controllers\BaseController;
use Yii;

/**
 * login controller for the `wechat` module
 */
class SendmsgController extends AController
{
    public $modelClass = '';


    /**
     * @SWG\Post(path="/officialaccount/sendmsg/send",
     *     tags={"微信消息"},
     *     summary="发送订单通知",
     *     @SWG\Response(
     *         response = 200,
     *         description = "发送订单通知"
     *     ),
     * @SWG\Parameter(
     *      name="access-token",
     *      type="string",
     *      in="query",
     *      required=true
     *  )
     * )
     *
     */
    public function actionSend()
    {

        // 支付金额
        // {{amount1.DATA}}

        // 订单编号
        // {{character_string2.DATA}}

        // 支付时间
        // {{date3.DATA}}

        // 商品详情
        // {{thing4.DATA}}

        $data = [
            'template_id' => '7QVEWkyRCBpGdOwThkw5Aln__L0xWxLgP5Qf7spPY9E', // 所需下发的订阅模板id
            'touser' => 'oE5EC0aqNTAdAXpPfikBpkHiSG1o',     // 接收者（用户）的 openid
            'page' => '',       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
            'data' => [         // 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
                'amount1' => [
                    'value' => 200,
                ],
                'character_string2' => [
                    'value' => 1000248,
                ],
                'date3' => [
                    'value' => 20200330,

                ],
                'thing4' => [
                    'value' => '买了什么东西',

                ]

            ],
        ];
        $miniProgram = Yii::$app->wechat->miniProgram;

        $miniProgram->subscribe_message->send($data);
    }
}
