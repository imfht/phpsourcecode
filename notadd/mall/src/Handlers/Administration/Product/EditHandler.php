<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-04-26 12:21
 */
namespace Notadd\Mall\Handlers\Administration\Product;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Product;

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
            'barcode'           => Rule::numeric(),
            'brand_id'          => [
                Rule::exists('mall_product_brands'),
                Rule::numeric(),
            ],
            'business_item'     => Rule::numeric(),
            'category_id'       => Rule::numeric(),
            'description'       => Rule::required(),
            'id'                => [
                Rule::numeric(),
                Rule::required(),
            ],
            'name'              => Rule::required(),
            'price'             => [
                Rule::numeric(),
                Rule::required(),
            ],
            'price_cost'        => [
                Rule::numeric(),
                Rule::required(),
            ],
            'price_market'      => Rule::numeric(),
            'inventory'         => [
                Rule::numeric(),
                Rule::required(),
            ],
            'inventory_warning' => Rule::numeric(),
        ], [
            'barcode.numeric'           => '商品条形码必须为数值',
            'brand_id.exists'           => '没有对应的品牌信息',
            'brand_id.numeric'          => '品牌 ID 必须为数值',
            'business_item.numeric'     => '商家货号必须为数值',
            'category_id.numeric'       => '分类 ID 必须为数值',
            'description.required'      => '商品描述必须填写',
            'id.required'               => '商品 ID 必须填写',
            'id.numeric'                => '商品 ID 必须为数值',
            'name.required'             => '商品名称必须填写',
            'price.numeric'             => '价格必须为数值',
            'price.required'            => '价格必须填写',
            'price_cost.numeric'        => '成本价格必须为数值',
            'price_cost.required'       => '成本价格必须填写',
            'price_market.numeric'      => '市场价格必须为数值',
            'inventory.numeric'         => '库存必须为数值',
            'inventory_warning.numeric' => '库存预警值必须为数值',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'barcode',
            'brand_id',
            'business_item',
            'category_id',
            'description',
            'name',
            'price',
            'price_cost',
            'price_market',
            'inventory',
            'inventory_warning',
        ]);
        $product = Product::query()->find($this->request->input('id'));
        if ($product instanceof Product && $product->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('修改商品信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的商品信息！');
        }
    }
}
