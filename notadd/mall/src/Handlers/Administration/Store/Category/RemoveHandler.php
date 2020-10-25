<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 12:11
 */
namespace Notadd\Mall\Handlers\Administration\Store\Category;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreCategory;

/**
 * Class RemoveHandler.
 */
class RemoveHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $this->validate($this->request, [
            'id' => [
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.numeric'  => '分类 ID 必须为数值',
            'id.required' => '分类 ID 必须填写',
        ]);
        $this->beginTransaction();
        $category = StoreCategory::query()->find($this->request->input('id'));
        if ($category instanceof StoreCategory && $category->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除店铺分类成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的店铺分类信息！');
        }
    }
}
