<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 17:36
 */
namespace Notadd\Mall\Handlers\Administration\Order\Exchange;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\OrderExchange;

/**
 * Class FinishHandler.
 */
class FinishHandler extends Handler
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
            'id.exists'   => '',
            'id.numeric'  => '',
            'id.required' => '',
        ]);
        $this->beginTransaction();
        $exchange = OrderExchange::query()->find($this->request->input('id'));
        if ($exchange instanceof OrderExchange && $exchange->update([])) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('商品换货订单结束成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('商品换货订单结束失败！');
        }
    }
}
