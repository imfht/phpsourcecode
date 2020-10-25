<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 17:34
 */
namespace Notadd\Mall\Handlers\User\Rate;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductRate;

/**
 * Class EditHandler.
 */
class EditHandler extends Handler
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
        $data = $this->request->only([]);
        if ($rate instanceof ProductRate && $rate->update($data)) {
            $this->withCode(200)->withMessage('编辑订单评价信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的订单评价信息！');
        }
    }
}
