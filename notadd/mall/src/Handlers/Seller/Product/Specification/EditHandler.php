<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 20:06
 */
namespace Notadd\Mall\Handlers\Seller\Product\Specification;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductSpecification;

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
            'category_id' => [
                Rule::exists('mall_product_categories'),
                Rule::numeric(),
                Rule::required(),
            ],
            'id'          => [
                Rule::exists('mall_product_categories'),
                Rule::numeric(),
                Rule::required(),
            ],
            'name'        => Rule::required(),
            'store_id'    => [
                Rule::exists('mall_stores'),
                Rule::numeric(),
                Rule::required(),
            ],
            'type'        => Rule::required(),
            'value'       => Rule::required(),
        ], [
            'category_id.exists'   => '没有对应的商品分类信息',
            'category_id.numeric'  => '商品分类 ID 必须为数值',
            'category_id.required' => '商品分类 ID 必须填写',
            'id.exists'            => '没有对应的商品规格信息',
            'id.numeric'           => '商品规格 ID 必须为数值',
            'id.required'          => '商品规格 ID 必须填写',
            'name.required'        => '规格显示名称必须填写',
            'store_id.exists'      => '没有对应的店铺分类信息',
            'store_id.numeric'     => '店铺 ID 必须为数值',
            'store_id.required'    => '店铺 ID 必须填写',
            'type.required'        => '规格类型必须填写',
            'value.required'       => '规格值必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'category_id',
            'name',
            'store_id',
            'type',
            'value',
        ]);
        $specification = ProductSpecification::query()->find($this->request->input('id'));
        if ($specification instanceof ProductSpecification && $specification->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('编辑商品规格成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withMessage('没有对应的商品规格信息！');
        }
    }
}
