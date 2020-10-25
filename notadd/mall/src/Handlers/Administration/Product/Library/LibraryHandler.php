<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-12 14:06
 */
namespace Notadd\Mall\Handlers\Administration\Product\Library;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductLibrary;

/**
 * Class LibraryHandler.
 */
class LibraryHandler extends Handler
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
                Rule::exists('mall_product_libraries'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的商品信息',
            'id.numeric'  => '商品 ID 必须为数值',
            'id.required' => '商品 ID 必须填写',
        ]);
        $builder = ProductLibrary::query();
        $builder->with('brand');
        $builder->with('category');
        $product = $builder->find($this->request->input('id'));
        if ($product instanceof ProductLibrary) {
            $this->withCode(200)->withData($product)->withMessage('获取商品信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的商品信息！');
        }
    }
}
