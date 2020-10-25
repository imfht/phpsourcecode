<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 18:00
 */
namespace Notadd\Mall\Handlers\Seller\Store\Navigation;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreNavigation;

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
    public function execute()
    {
        $this->validate($this->request, [
            'is_show'       => Rule::numeric(),
            'name'          => Rule::required(),
            'order'         => Rule::numeric(),
            'parent_target' => Rule::numeric(),
            'store_id'      => [
                Rule::exists('mall_stores'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'is_show.numeric'       => '是否显示的值必须为数值',
            'name.required'         => '导航名称必须填写',
            'order.numeric'         => '排序的值必须为数值',
            'parent_target.numeric' => '新窗口打开的值必须为数值',
            'store_id.exists'       => '没有对应的店铺信息',
            'store_id.numeric'      => '店铺 ID 必须为数值',
            'store_id.required'     => '店铺 ID 必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'is_show',
            'name',
            'order',
            'parent_target',
            'store_id',
            'url',
        ]);
        if (StoreNavigation::query()->create($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('创建店铺导航成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('创建店铺导航失败！');
        }
    }
}
