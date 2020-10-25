<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 20:08
 */
namespace Notadd\Mall\Handlers\Seller\Product\Specification;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductSpecification;

/**
 * Class SpecificationsHandler.
 */
class SpecificationHandler extends Handler
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
                Rule::exists('mall_product_categories'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的商品规格信息',
            'id.numeric'  => '商品规格 ID 必须为数值',
            'id.required' => '商品规格 ID 必须填写',
        ]);
        $specification = ProductSpecification::query()->find($this->request->input('id'));
        if ($specification instanceof ProductSpecification) {
            $this->withCode(200)->withData($specification)->withMessage('获取商品规格数据成功！');
        } else {
            $this->withCode(500)->withError('没有对应的商品规格信息！');
        }
    }
}
