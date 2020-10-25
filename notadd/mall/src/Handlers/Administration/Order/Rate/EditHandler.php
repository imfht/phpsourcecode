<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 15:38
 */
namespace Notadd\Mall\Handlers\Administration\Order\Rate;

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
            'comment' => Rule::required(),
            'id'      => [
                Rule::exists('mall_order_rates'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'comment.required' => '商品评价信息必须填写',
            'id.exists'        => '没有对应的商品评价信息',
            'id.numeric'       => '商品 ID 必须为数值',
            'id.required'      => '商品 ID 必须填写',
        ]);
        $this->beginTransaction();
        $rate = ProductRate::query()->find($this->request->input('id'));
        $data = $this->request->only([
            'comment',
        ]);
        if ($rate instanceof ProductRate && $rate->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('更新商品评价信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withMessage('更新商品评价信息失败！');
        }
    }
}
