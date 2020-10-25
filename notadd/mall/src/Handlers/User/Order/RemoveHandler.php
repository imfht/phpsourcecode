<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 13:22
 */
namespace Notadd\Mall\Handlers\User\Order;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Order;

/**
 * Class RemoveHandler.
 */
class RemoveHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    public function execute()
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
        if ($order instanceof Order && $order->delete()) {
            $this->withCode(200)->withMessage('删除订单信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的订单信息！');
        }
    }
}
