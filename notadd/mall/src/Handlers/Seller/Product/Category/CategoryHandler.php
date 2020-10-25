<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-23 16:34
 */
namespace Notadd\Mall\Handlers\Seller\Product\Category;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductCategory;

/**
 * Class CategoryHandler.
 */
class CategoryHandler extends Handler
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
            'id.exists'   => '没有对应的分类信息',
            'id.numeric'  => '分类 ID 必须为数值',
            'id.required' => '分类 ID 必须填写',
        ]);
        $category = ProductCategory::query()->find($this->request->input('id'));
        if ($category instanceof ProductCategory) {
            $this->withCode(200)->withData($category)->withMessage('获取分类信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的分类信息！');
        }
    }
}
