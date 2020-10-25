<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 17:08
 */
namespace Notadd\Mall\Handlers\Store\Product;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Product;

/**
 * Class ProductHandler.
 */
class ProductHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'id' => [
                Rule::required(),
                Rule::exists('mall_products'),
            ],
        ], [
            'id.exists'   => '没有对应的商品信息',
            'id.required' => '商品 ID 必须填写',
        ]);
        $product = Product::query()->find($this->request->input('id'));
        if ($product instanceof Product) {
            $this->withCode(200)->withData($product)->withMessage('获取商品信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的商品信息！');
        }
    }
}
