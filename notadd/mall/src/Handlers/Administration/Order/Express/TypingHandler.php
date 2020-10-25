<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 16:51
 */
namespace Notadd\Mall\Handlers\Administration\Order\Express;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\OrderExpress;

/**
 * Class TypingHandler.
 */
class TypingHandler extends Handler
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
                Rule::exists('mall_order_expresses'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的订单物流信息',
            'id.numeric'  => '订单物流 ID 必须为数值',
            'id.required' => '订单物流 ID 必须填写',
        ]);
        $exchange = OrderExpress::query()->find($this->request->input('id'));
        if ($exchange instanceof OrderExpress) {
            $this->withCode(200)->withMessage('更新订单物流信息成功！');
        } else {
            $this->withCode(500)->withError('更新订单物流信息失败！');
        }
    }
}
