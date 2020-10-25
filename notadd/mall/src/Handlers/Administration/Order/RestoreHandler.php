<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-04-26 14:54
 */
namespace Notadd\Mall\Handlers\Administration\Order;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Order;

/**
 * Class RestoreHandler.
 */
class RestoreHandler extends Handler
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
        $this->beginTransaction();
        $order = Order::onlyTrashed()->find($this->request->input('id'));
        if ($order && $order->restore()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('');
        }
    }
}
