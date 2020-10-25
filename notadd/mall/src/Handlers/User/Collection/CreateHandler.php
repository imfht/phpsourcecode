<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 16:50
 */
namespace Notadd\Mall\Handlers\User\Collection;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\UserCollection;

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
            'product_id' => [
                Rule::exists('mall_products'),
                Rule::numeric(),
                Rule::required(),
            ],
            'store_id'   => [
                Rule::exists('mall_stores'),
                Rule::numeric(),
                Rule::required(),
            ],
            'user_id'    => [
                Rule::exists('mall_users'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'product_id.exists'   => '没有对应的商品信息',
            'product_id.numeric'  => '商品 ID 必须为数值',
            'product_id.required' => '商品 ID 必须填写',
            'store_id.exists'     => '没有对应的店铺信息',
            'store_id.numeric'    => '店铺 ID 必须为数值',
            'store_id.required'   => '店铺 ID 必须填写',
            'user_id.exists'      => '没有对应的用户信息',
            'user_id.numeric'     => '用户 ID 必须为数值',
            'user_id.required'    => '用户 ID 必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'product_id',
            'store_id',
            'user_id',
        ]);
        if (UserCollection::query()->create($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('创建用户收藏信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('创建用户收藏信息失败！');
        }
    }
}
