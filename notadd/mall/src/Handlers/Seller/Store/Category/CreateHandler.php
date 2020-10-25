<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 18:22
 */
namespace Notadd\Mall\Handlers\Seller\Store\Category;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreCategory;

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
            'name'     => Rule::required(),
            'store_id' => [
                Rule::exists('mall_stores'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'name.required'     => '分类名称必须填写',
            'store_id.exists'   => '没有对应的店铺信息',
            'store_id.numeric'  => '店铺 ID 必须数值',
            'store_id.required' => '店铺 ID 必须为填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'parent_id',
            'name',
            'store_id',
        ]);
        if (StoreCategory::query()->create($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('创建店铺分类成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('创建店铺分类失败！');
        }
    }
}
