<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 13:37
 */
namespace Notadd\Mall\Handlers\User\Cart;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\UserCart;

/**
 * Class CardHandler.
 */
class CardHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'user_id' => [
                Rule::exists('mall_users'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'user_id.exists'   => '没有对应的用户信息',
            'user_id.numeric'  => '用户 ID 必须为数值',
            'user_id.required' => '用户 ID 必须填写',
        ]);
        $cart = UserCart::query()->where('user_id', $this->request->input('user_id'))->get();
        $this->withCode(200)->withData($cart)->withMessage('获取购物车信息成功！');
    }
}
