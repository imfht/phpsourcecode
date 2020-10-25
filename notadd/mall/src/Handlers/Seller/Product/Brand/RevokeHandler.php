<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 18:39
 */
namespace Notadd\Mall\Handlers\Seller\Product\Brand;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductBrand;

/**
 * Class RevokeHandler.
 */
class RevokeHandler extends Handler
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
                Rule::exists('mall_product_brands'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的品牌信息',
            'id.numeric'  => '品牌 ID 必须为数值',
            'id.required' => '品牌 ID 必须填写',
        ]);
        $brand = ProductBrand::query()->find($this->request->input('id'));
        $this->beginTransaction();
        if ($brand instanceof ProductBrand && $brand->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('撤销品牌成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的品牌信息！');
        }
    }
}
