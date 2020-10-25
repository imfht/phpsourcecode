<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 15:25
 */
namespace Notadd\Mall\Handlers\Administration\Order\Refund;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\OrderRefund;

/**
 * Class ConfirmHandler.
 */
class ConfirmHandler extends Handler
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
                Rule::exists('mall_order_refunds'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的订单退货信息',
            'id.numeric'  => '订单 ID 必须为数值',
            'id.required' => '订单 ID 必须填写',
        ]);
        $refund = OrderRefund::query()->find($this->request->input('id'));
        if ($refund instanceof OrderRefund) {
            $this->withCode(200)->withData($refund)->withMessage('获取订单退货信息成功！');
        } else {
            $this->withCode(500)->withError('获取订单退货信息失败！');
        }
    }
}
