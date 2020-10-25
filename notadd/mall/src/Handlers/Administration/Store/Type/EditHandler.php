<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-27 19:24
 */
namespace Notadd\Mall\Handlers\Administration\Store\Type;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreType;

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
            'amount_of_deposit' => Rule::numeric(),
            'id'                => [
                Rule::exists('mall_store_types'),
                Rule::numeric(),
                Rule::required(),
            ],
            'name'              => Rule::required(),
            'order'             => Rule::numeric(),
        ], [
            'amount_of_deposit.numeric' => '保证金数额必须为数值',
            'id.exists'                 => '没有对应的店铺类型信息',
            'id.numeric'                => '店铺类型 ID 必须为数值',
            'id.required'               => '店铺类型 ID 必须填写',
            'name.required'             => '类型名称必须填写',
            'order.numeric'             => '排序必须为数值',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'amount_of_deposit',
            'name',
            'order',
        ]);
        $type = StoreType::query()->find($this->request->input('id'));
        if ($type instanceof StoreType && $type->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('编辑店铺类型信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('编辑店铺类型信息失败！');
        }
    }
}
