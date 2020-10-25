<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 17:36
 */
namespace Notadd\Mall\Handlers\User\Rate;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductRate;

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
                Rule::exists('mall_order_rates'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的订单评价信息',
            'id.required' => '订单 ID 必须填写',
            'id.numeric'  => '订单 ID 必须为数值',
        ]);
        $this->beginTransaction();
        $rate = ProductRate::query()->find($this->request->input('id'));
        if ($rate instanceof ProductRate && $rate->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除订单评论成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的订单评论信息');
        }
    }
}
