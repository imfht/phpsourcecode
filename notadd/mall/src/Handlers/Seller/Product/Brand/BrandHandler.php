<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 18:40
 */
namespace Notadd\Mall\Handlers\Seller\Product\Brand;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductBrand;

/**
 * Class BrandHandler.
 */
class BrandHandler extends Handler
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
                Rule::exists('mall_product_brands'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的品牌信息',
            'id.numeric'  => '品牌 ID 必须为数值',
            'id.required' => '品牌 ID 必须填写',
        ]);
        $brand = ProductBrand::query()->find($this->request->input('id'));
        if ($brand instanceof ProductBrand) {
            $this->withCode(200)->withData($brand)->withMessage('获取品牌信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的品牌信息！');
        }
    }
}
