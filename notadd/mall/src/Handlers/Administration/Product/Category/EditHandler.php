<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-04-25 17:21
 */
namespace Notadd\Mall\Handlers\Administration\Product\Category;

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
    public function execute()
    {
        $this->validate($this->request, [
            'deposit'   => [
                Rule::numeric(),
                Rule::required(),
            ],
            'id'        => [
                Rule::numeric(),
                Rule::required(),
            ],
            'name'      => Rule::required(),
            'parent_id' => Rule::numeric(),
            'order'     => Rule::numeric(),
        ], [
            'deposit.numeric'   => '保证金数额必须为数值',
            'deposit.required'  => '保证金数额必须填写',
            'id.required'       => '分类 ID 必须填写',
            'id.numeric'        => '分类 ID 必须为数值',
            'name.required'     => '分类名称必须填写',
            'parent_id.numeric' => '父级分类 ID 必须为数值',
            'order.numeric'     => '排序必须为数值',
        ]);
        $this->beginTransaction();
        $category = ProductCategory::query()->find($this->request->input('id'));
        $data = $this->request->only([
            'deposit',
            'logo',
            'name',
            'parent_id',
            'order',
        ]);
        if ($category instanceof ProductCategory && $category->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('编辑分类信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的分类信息！');
        }
    }
}
