<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-27 14:57
 */
namespace Notadd\Mall\Handlers\Seller\Product\Category;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductCategory;

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
    protected function execute()
    {
        $this->validate($this->request, [
            'deposit'   => Rule::numeric(),
            'id'        => [
                Rule::exists('mall_product_categories'),
                Rule::numeric(),
                Rule::required(),
            ],
            'parent_id' => [
                Rule::exists('mall_product_categories'),
                Rule::numeric(),
            ],
            'name'      => Rule::required(),
        ], [
            'deposit.numeric'   => '品牌 ID 必须为数值',
            'id.exists'         => '没有对应的商品分类信息',
            'id.required'       => '商品分类 ID 必须填写',
            'id.numeric'        => '商品分类 ID 必须为数值',
            'parent_id.exists'  => '没有对应的商品分类信息',
            'parent_id.numeric' => '分类 ID 必须为数值',
            'name.required'     => '商品名称必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'deposit',
            'parent_id',
            'name',
        ]);
        $product = ProductCategory::query()->find($this->request->input('id'));
        if ($product instanceof ProductCategory && $product->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('修改商品信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的商品信息！');
        }
    }
}