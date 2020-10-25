<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-22 20:25
 */
namespace Notadd\Mall\Handlers\Administration\Order;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Order;

/**
 * Class OrderHandler.
 */
class OrderHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'id' => [
                Rule::exists('mall_orders'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的订单信息',
            'id.required' => '订单 ID 必须填写',
            'id.numeric'  => '订单 ID 必须为数值',
        ]);
        $order = Order::query()->find($this->request->input('id'));
        if ($order instanceof Order) {
            $this->withCode(200)->withData($order)->withMessage('获取订单信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的订单信息！');
        }
    }
}