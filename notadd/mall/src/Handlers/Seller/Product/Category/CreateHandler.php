<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-27 14:52
 */
namespace Notadd\Mall\Handlers\Seller\Product\Category;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductCategory;

/**
 * Class CreateHandler.
 */
class CreateHandler extends Handler
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
            'parent_id' => [
                Rule::exists('mall_product_categories'),
                Rule::numeric(),
            ],
            'name'      => Rule::required(),
        ], [
            'deposit.numeric'   => '品牌 ID 必须为数值',
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
        if (ProductCategory::query()->create($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('添加商品成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('添加商品失败！');
        }
    }
}
