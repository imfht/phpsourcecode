<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-27 17:19
 */
namespace Notadd\Mall\Handlers\Seller\Store\Supplier;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreSupplier;

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
            'contacts'  => Rule::required(),
            'id'        => [
                Rule::exists('mall_store_suppliers'),
                Rule::numeric(),
                Rule::required(),
            ],
            'name'      => Rule::required(),
            'store_id'  => [
                Rule::exists('mall_stores'),
                Rule::numeric(),
                Rule::required(),
            ],
            'telephone' => Rule::required(),
        ], [
            'contacts.required'  => '联系人必须填写',
            'id.exists'          => '没有对应的店铺供应商信息',
            'id.numeric'         => '店铺供应商 ID 必须为数值',
            'id.required'        => '店铺供应商 ID 必须填写',
            'name.required'      => '供货商名称必须填写',
            'store_id.exists'    => '没有对应的店铺信息',
            'store_id.numeric'   => '店铺 ID 必须为数值',
            'store_id.required'  => '店铺 ID 必须填写',
            'telephone.required' => '联系电话必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'store_id',
            'name',
            'contacts',
            'telephone',
            'comments',
        ]);
        $supplier = StoreSupplier::query()->find($this->request->input('id'));
        if ($supplier instanceof StoreSupplier && $supplier->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('编辑供应商信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的供应商信息！');
        }
    }
}
