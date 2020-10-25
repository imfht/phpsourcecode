<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 17:34
 */
namespace Notadd\Mall\Handlers\Administration\Order\Exchange;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\OrderExchange;

/**
 * Class SendHandler.
 */
class SendHandler extends Handler
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
                Rule::exists('mall_order_exchanges'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的商品交换订单信息',
            'id.numeric'  => '商品交换订单 ID 必须为数值',
            'id.required' => '商品交换订单 ID 必须填写',
        ]);
        $id = $this->request->input('id');
        $exchange = OrderExchange::query()->find($id);
        if ($exchange instanceof OrderExchange) {
            $this->withCode(200)->withMessage('商品交换订单重新发货成功！');
        } else {
            $this->withCode(500)->withError('商品交换订单重新发货失败！');
        }
    }
}
