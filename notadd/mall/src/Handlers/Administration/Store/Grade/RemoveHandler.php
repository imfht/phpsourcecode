<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-15 19:47
 */
namespace Notadd\Mall\Handlers\Administration\Store\Grade;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreGrade;

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
    protected function execute()
    {
        $this->validate($this->request, [
            'id' => [
                Rule::exists('mall_store_grades'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的店铺等级信息',
            'id.numeric'  => '店铺等级 ID 必须为数值',
            'id.required' => '店铺等级 ID 必须填写',
        ]);
        $this->beginTransaction();
        $grade = StoreGrade::query()->find($this->request->input('id'));
        if ($grade instanceof StoreGrade && $grade->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除店铺等级信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('删除店铺等级信息失败！');
        }
    }
}
