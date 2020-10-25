<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-27 17:21
 */
namespace Notadd\Mall\Handlers\Seller\Store\Supplier;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreSupplier;

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
                Rule::exists('mall_store_suppliers'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的店铺供应商信息',
            'id.numeric'  => '店铺供应商 ID 必须为数值',
            'id.required' => '店铺供应商 ID 必须填写',
        ]);
        $this->beginTransaction();
        $supplier = StoreSupplier::query()->find($this->request->input('id'));
        if ($supplier instanceof StoreSupplier && $supplier->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除供应商信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的供应商信息！');
        }
    }
}
