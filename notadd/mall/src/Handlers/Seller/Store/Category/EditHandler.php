<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 18:27
 */
namespace Notadd\Mall\Handlers\Seller\Store\Category;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreCategory;

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
            'id'     => [
                Rule::exists('mall_store_categories'),
                Rule::numeric(),
                Rule::required(),
            ],
            'name'   => Rule::required(),
            'status' => Rule::numeric(),
        ], [
            'id.exists'      => '没有对应的店铺分类信息',
            'id.numeric'     => '分类 ID 必须为数值',
            'id.required'    => '分类 ID 必须填写',
            'name.required'  => '分类名称必须填写',
            'status.numeric' => '状态值必须数值',
        ]);
        $this->beginTransaction();
        $category = StoreCategory::query()->find($this->request->input('id'));
        $data = $this->request->only([
            'parent_id',
            'name',
            'status',
            'store_id',
        ]);
        if ($category && $category->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('编辑分类信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的分类信息！');
        }
    }
}
