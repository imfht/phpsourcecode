<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-27 17:35
 */
namespace Notadd\Mall\Handlers\Seller\Store\Outlet;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreOutlet;

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
                Rule::exists('mall_store_outlets'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的店铺门店信息',
            'id.required' => '店铺门店 ID 必须填写',
            'id.numeric'  => '店铺门店 ID 必须为数值',
        ]);
        $this->beginTransaction();
        $outlet = StoreOutlet::query()->find($this->request->input('id'));
        if ($outlet instanceof StoreOutlet && $outlet->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除门店成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的门店信息！');
        }
    }
}
