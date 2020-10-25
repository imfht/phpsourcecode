<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-27 20:13
 */
namespace Notadd\Mall\Handlers\Administration\Store\Type;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreType;

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
            'amount_of_deposit' => Rule::numeric(),
            'name'              => Rule::required(),
            'order'             => Rule::numeric(),
        ], [
            'amount_of_deposit.numeric' => '保证金数额必须为数值',
            'name.required'             => '类型名称必须填写',
            'order.numeric'             => '排序必须为数值',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'amount_of_deposit',
            'name',
            'order',
        ]);
        if (StoreType::query()->create($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('添加店铺类型信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('添加店铺类型信息失败！');
        }
    }
}
