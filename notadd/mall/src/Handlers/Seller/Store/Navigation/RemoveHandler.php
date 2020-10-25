<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 18:05
 */
namespace Notadd\Mall\Handlers\Seller\Store\Navigation;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreNavigation;

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
                Rule::exists('mall_store_navigations'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的店铺导航信息',
            'id.required' => '店铺导航 ID 必须填写',
            'id.numeric'  => '店铺导航 ID 必须为数值',
        ]);
        $this->commitTransaction();
        $navigation = StoreNavigation::query()->find($this->request->input('id'));
        if ($navigation instanceof StoreNavigation && $navigation->delete()) {
            $this->withCode(200)->withMessage('删除店铺导航成功！');
        } else {
            $this->withCode(500)->withError('没有对应的店铺导航信息！');
        }
    }
}
