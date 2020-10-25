<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 11:57
 */
namespace Notadd\Mall\Handlers\Administration\Store\Category;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreCategory;

/**
 * Class ListHandler.
 */
class ListHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'store_id' => [
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'store_id.required' => '店铺 ID 必须填写',
            'store_id.numeric'  => '店铺 ID 必须为数值',
        ]);
        $builder = StoreCategory::query();
        $builder->with('children.children.children');
        $builder->where('store_id', $this->request->input('store_id'));
        $this->withCode(200)->withData($builder->get())->withMessage('获取商品列表成功！');
    }
}
