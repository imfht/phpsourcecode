<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 17:06
 */
namespace Notadd\Mall\Handlers\Store\Category;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreCategory;

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
                Rule::exists('mall_shop_categories'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的店铺分类信息',
            'id.numeric'  => '分类 ID 必须为数值',
            'id.required' => '分类 ID 必须填写',
        ]);
        $category = StoreCategory::query()->find($this->request->input('id'));
        if ($category instanceof StoreCategory) {
            $this->withCode(200)->withData($category)->withMessage('获取店铺分类信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的店铺分类信息！');
        }
    }
}
