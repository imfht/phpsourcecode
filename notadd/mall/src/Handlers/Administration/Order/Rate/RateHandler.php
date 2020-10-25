<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 15:40
 */
namespace Notadd\Mall\Handlers\Administration\Order\Rate;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductRate;

/**
 * Class RateHandler.
 */
class RateHandler extends Handler
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
            'id.exists'   => '没有对应的商品评价信息',
            'id.numeric'  => '商品 ID 必须为数值',
            'id.required' => '商品 ID 必须填写',
        ]);
        $rate = ProductRate::query()->find($this->request->input('id'));
        if ($rate instanceof ProductRate) {
            $this->withCode(200)->withData($rate)->withMessage('获取商品评价信息成功！');
        } else {
            $this->withCode(500)->withMessage('获取商品评价信息失败！');
        }
    }
}
