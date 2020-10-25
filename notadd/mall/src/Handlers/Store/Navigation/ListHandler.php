<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-29 12:18
 */
namespace Notadd\Mall\Handlers\Store\Navigation;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreNavigation;

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
                Rule::exists('mall_stores'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'store_id.exists'   => '没有对应的店铺信息',
            'store_id.required' => '店铺 ID 必须填写',
            'store_id.numeric'  => '店铺 ID 必须为数值',
        ]);
        $builder = StoreNavigation::query();
        $data = $builder->where('store_id', $this->request->input('store_id'))->get();
        $this->withCode(200)->withData($data)->withMessage('获取店铺导航列表成功！');
    }
}
