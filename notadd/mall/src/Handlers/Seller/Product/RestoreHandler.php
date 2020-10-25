<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-23 14:08
 */
namespace Notadd\Mall\Handlers\Seller\Product;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Product;

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
    protected function execute()
    {
        $this->validate($this->request, [
            'id' => [
                Rule::exists('mall_products'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的商品信息',
            'id.numeric'  => '商品 ID 必须为数值',
            'id.required' => '商品 ID 必须填写',
        ]);
        $this->beginTransaction();
        $product = Product::query()->onlyTrashed()->find($this->request->input('id'));
        if ($product instanceof Product && $product->restore()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('恢复商品成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的商品信息！');
        }
    }
}
